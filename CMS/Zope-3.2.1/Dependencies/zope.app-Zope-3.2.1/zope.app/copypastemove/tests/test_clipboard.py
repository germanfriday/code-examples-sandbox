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
"""Clipboard tests

$Id: test_clipboard.py 29143 2005-02-14 22:43:16Z srichter $
"""
import unittest 

from zope.app import zapi
from zope.app.annotation.interfaces import IAnnotations
from zope.app.component.testing import PlacefulSetup
from zope.app.principalannotation import PrincipalAnnotationUtility
from zope.app.principalannotation.interfaces import IPrincipalAnnotationUtility
from zope.app.testing import ztapi

from zope.app.copypastemove.interfaces import IPrincipalClipboard
from zope.app.copypastemove import PrincipalClipboard


class PrincipalStub(object):

    def __init__(self, id):
        self.id = id


class PrincipalClipboardTest(PlacefulSetup, unittest.TestCase):

    def setUp(self):
        self.buildFolders()

        ztapi.provideAdapter(IAnnotations, IPrincipalClipboard,
                             PrincipalClipboard)
        ztapi.provideUtility(IPrincipalAnnotationUtility,
                             PrincipalAnnotationUtility())

    def testAddItems(self):
        user = PrincipalStub('srichter')

        annotationutil = zapi.getUtility(IPrincipalAnnotationUtility)
        annotations = annotationutil.getAnnotations(user)
        clipboard = IPrincipalClipboard(annotations)
        clipboard.addItems('move', ['bla', 'bla/foo', 'bla/bar'])
        expected = ({'action':'move', 'target':'bla'},
                    {'action':'move', 'target':'bla/foo'},
                    {'action':'move', 'target':'bla/bar'})

        self.failUnless(clipboard.getContents() == expected)
        clipboard.addItems('copy', ['bla'])
        expected = expected + ({'action':'copy', 'target':'bla'},)
        self.failUnless(clipboard.getContents() == expected)

    def testSetContents(self):
        user = PrincipalStub('srichter')

        annotationutil = zapi.getUtility(IPrincipalAnnotationUtility)
        annotations = annotationutil.getAnnotations(user)
        clipboard = IPrincipalClipboard(annotations)

        expected = ({'action':'move', 'target':'bla'},
                    {'action':'move', 'target':'bla/foo'},
                    {'action':'move', 'target':'bla/bar'})
        clipboard.setContents(expected)
        self.failUnless(clipboard.getContents() == expected)
        clipboard.addItems('copy', ['bla'])
        expected = expected + ({'action':'copy', 'target':'bla'},)
        self.failUnless(clipboard.getContents() == expected)

    def testClearContents(self):
        user = PrincipalStub('srichter')

        annotationutil = zapi.getUtility(IPrincipalAnnotationUtility)
        annotations = annotationutil.getAnnotations(user)
        clipboard = IPrincipalClipboard(annotations)
        clipboard.clearContents()
        self.failUnless(clipboard.getContents() == ())

def test_suite():
    return unittest.TestSuite((
        unittest.makeSuite(PrincipalClipboardTest),
        ))

if __name__=='__main__':
    unittest.main(defaultTest='test_suite')

