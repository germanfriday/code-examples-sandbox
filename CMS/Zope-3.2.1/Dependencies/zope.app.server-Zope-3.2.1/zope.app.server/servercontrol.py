##############################################################################
#
# Copyright (c) 2001,2002,2003 Zope Corporation and Contributors.
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
"""Server Control Implementation

$Id: servercontrol.py 26551 2004-07-15 07:06:37Z srichter $
"""
from ThreadedAsync import LoopCallback
from zope.app.applicationcontrol.interfaces import IServerControl
from zope.interface import implements

class ServerControl(object):

    implements(IServerControl)

    def shutdown(self, time=0):
        """See zope.app.applicationcontrol.interfaces.IServerControl"""
        # TODO: Graceful shutdown does not work yet.

        # This will work for servers started directly and by zdaemon. Passing
        # an exit status of 0 causes zdaemon to not restart the process.
        LoopCallback.exit_status = 0

    def restart(self, time=0):
        """See zope.app.applicationcontrol.interfaces.IServerControl"""
        # TODO: Graceful restart does not work yet.

        # TODO: Make sure this is only called if we are running via zdaemon.

        # Passing an exit status of 1 causes zdaemon to restart the process.
        LoopCallback.exit_status = 1
        

serverControl = ServerControl()
