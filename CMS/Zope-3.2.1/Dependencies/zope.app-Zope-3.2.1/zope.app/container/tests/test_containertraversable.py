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
"""Container Traverser tests.

$Id: test_containertraversable.py 28263 2004-10-26 22:59:30Z jim $
"""
import unittest
from zope.app.container.traversal import ContainerTraversable
from zope.app.traversing.interfaces import TraversalError
from zope.app.container.interfaces import IContainer
from zope.testing.cleanup import CleanUp
from zope.interface import implements


class Container(object):

    implements(IContainer)

    def __init__(self, attrs={}, objs={}):
        for attr,value in attrs.iteritems():
            setattr(self, attr, value)

        self.__objs = {}
        for name,value in objs.iteritems():
            self.__objs[name] = value


    def __getitem__(self, name):
        return self.__objs[name]

    def get(self, name, default=None):
        return self.__objs.get(name, default)

    def __contains__(self, name):
        return self.__objs.has_key(name)


class Test(CleanUp, unittest.TestCase):
    def testAttr(self):
        # test container path traversal
        foo = Container()
        bar = Container()
        baz = Container()
        c   = Container({'foo': foo}, {'bar': bar, 'foo': baz})

        T = ContainerTraversable(c)
        self.failUnless(T.traverse('foo', []) is baz)
        self.failUnless(T.traverse('bar', []) is bar)

        self.assertRaises(TraversalError , T.traverse, 'morebar', [])


def test_suite():
    loader = unittest.TestLoader()
    return loader.loadTestsFromTestCase(Test)


if __name__ == '__main__':
    unittest.TextTestRunner().run(test_suite())
