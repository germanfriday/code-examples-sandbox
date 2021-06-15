##############################################################################
#
# Copyright (c) 2004 Zope Corporation and Contributors.
# All Rights Reserved.
#
# This software is subject to the provisions of the Zope Public License,
# Version 2.0 (ZPL).  A copy of the ZPL should accompany this distribution.
# THIS SOFTWARE IS PROVIDED "AS IS" AND ANY AND ALL EXPRESS OR IMPLIED
# WARRANTIES ARE DISCLAIMED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF TITLE, MERCHANTABILITY, AGAINST INFRINGEMENT, AND FITNESS
# FOR A PARTICULAR PURPOSE.
#
##############################################################################
"""XXX short summary goes here.

$Id: test_asyncore_main_loop.py 40849 2005-12-17 16:20:17Z jim $
"""

import asyncore
import unittest
from zope.testing import doctest

def setUp(test):
    test.globs['saved-socket-map'] = asyncore.socket_map.copy()
    asyncore.socket_map.clear()

def tearDown(test):
    test.globs['handler'].uninstall()
    asyncore.socket_map.clear()
    asyncore.socket_map.update(test.globs['saved-socket-map'])

def test_suite():
    return doctest.DocTestSuite('zope.app.twisted.asyncore_main_loop',
                                setUp=setUp, tearDown=tearDown)

if __name__ == '__main__':
    unittest.main(defaultTest='test_suite')

