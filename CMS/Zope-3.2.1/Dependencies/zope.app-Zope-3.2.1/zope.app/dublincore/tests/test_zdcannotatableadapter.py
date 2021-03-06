##############################################################################
#
# Copyright (c) 2001, 2002 Zope Corporation and Contributors.
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
"""Test the Dublin Core annotations adapter.

$Id: test_zdcannotatableadapter.py 29143 2005-02-14 22:43:16Z srichter $
"""
import unittest

from zope.app.annotation.interfaces import IAnnotations
from zope.app.testing.placelesssetup import PlacelessSetup
from zope.interface import implements

class TestAnnotations(dict):

    implements(IAnnotations)


class DublinCoreAdapterTest(PlacelessSetup, unittest.TestCase):

    def testZDCAnnotatableAdapter(self):

        from zope.app.dublincore.annotatableadapter \
             import ZDCAnnotatableAdapter

        annotations = TestAnnotations()
        dc = ZDCAnnotatableAdapter(annotations)

        self.failIf(annotations, "There shouldn't be any data yet")
        self.assertEqual(dc.title, u'')
        self.failIf(annotations, "There shouldn't be any data yet")
        dc.title = u"Test title"
        self.failUnless(annotations, "There should be data now!")

        dc = ZDCAnnotatableAdapter(annotations)
        self.assertEqual(dc.title, u'Test title')




def test_suite():
    return unittest.makeSuite(DublinCoreAdapterTest)

if __name__=='__main__':
    unittest.main(defaultTest='test_suite')
