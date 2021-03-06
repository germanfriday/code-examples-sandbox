##############################################################################
#
# Copyright (c) 2003 Zope Corporation and Contributors.
# All Rights Reserved.
#
# This software is subject to the provisions of the Zope Public License,
# Version 2.1 (ZPL).  A copy of the ZPL should accompany this distribution.
# THIS SOFTWARE IS PROVIDED "AS IS" AND ANY AND ALL EXPRESS OR IMPLIED
# WARRANTIES ARE DISCLAIMED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF TITLE, MERCHANTABILITY, AGAINST INFRINGEMENT, AND FITNESS
# FOR A PARTICULAR PURPOSE.
#
##############################################################################
"""Functions that control how the Zope appserver knits itself together.

$Id: main.py 39539 2005-10-20 15:07:29Z fdrake $
"""
import logging
import os
import sys
import time

from zdaemon import zdoptions

import ThreadedAsync

import zope.app.appsetup.appsetup
import zope.app.appsetup.interfaces
from zope.event import notify
from zope.server.taskthreads import ThreadedTaskDispatcher

CONFIG_FILENAME = "zope.conf"

class ZopeOptions(zdoptions.ZDOptions):

    logsectionname = None

    def default_configfile(self):
        dir = os.path.normpath(
            os.path.join(os.path.dirname(__file__),
                         os.pardir, os.pardir, os.pardir, os.pardir))
        for filename in [CONFIG_FILENAME, CONFIG_FILENAME + ".in"]:
            filename = os.path.join(dir, filename)
            if os.path.isfile(filename):
                return filename
        return None


def main(args=None):
    # Record start times (real time and CPU time)
    t0 = time.time()
    c0 = time.clock()

    setup(load_options(args))

    t1 = time.time()
    c1 = time.clock()
    logging.info("Startup time: %.3f sec real, %.3f sec CPU", t1-t0, c1-c0)

    run()
    sys.exit(0)


def debug(args=None):
    options = load_options(args)

    zope.app.appsetup.config(options.site_definition)

    db = zope.app.appsetup.appsetup.multi_database(options.databases)[0][0]
    notify(zope.app.appsetup.interfaces.DatabaseOpened(db))
    return db


def run():
    try:
        ThreadedAsync.loop()
    except KeyboardInterrupt:
        # Exit without spewing an exception.
        pass


def load_options(args=None):
    if args is None:
        args = sys.argv[1:]
    options = ZopeOptions()
    options.schemadir = os.path.dirname(os.path.abspath(__file__))
    options.realize(args)
    options = options.configroot

    if options.path:
        sys.path[:0] = [os.path.abspath(p) for p in options.path]
    return options


def setup(options):
    sys.setcheckinterval(options.check_interval)

    options.eventlog()
    options.accesslog()

    zope.app.appsetup.config(options.site_definition,
                             features=('zserver',))

    db = zope.app.appsetup.appsetup.multi_database(options.databases)[0][0]

    notify(zope.app.appsetup.interfaces.DatabaseOpened(db))

    task_dispatcher = ThreadedTaskDispatcher()
    task_dispatcher.setThreadCount(options.threads)

    for server in options.servers:
        server.create(task_dispatcher, db)

    notify(zope.app.appsetup.interfaces.ProcessStarting())

    return db
