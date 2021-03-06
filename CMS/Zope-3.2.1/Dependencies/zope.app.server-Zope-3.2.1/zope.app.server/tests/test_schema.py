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
"""Test that the Zope appserver configuration schema can be loaded.

$Id: test_schema.py 25177 2004-06-02 13:17:31Z jim $
"""

import os.path
import unittest

import ZConfig


class TestConfiguration(unittest.TestCase):

    def test_schema(self):
        dir = os.path.dirname(os.path.dirname(__file__))
        filename = os.path.join(dir, "schema.xml")
        ZConfig.loadSchema(filename)


def test_suite():
    return unittest.makeSuite(TestConfiguration)

if __name__ == "__main__":
    try:
        __file__
    except NameError:
        import sys
        __file__ = sys.argv[0]
    unittest.main(defaultTest="test_suite")
