# -*- test-case-name: twisted.web.test.test_xmlrpc -*-
#
# Copyright (c) 2001-2004 Twisted Matrix Laboratories.
# See LICENSE for details.

# 

"""Test XML-RPC support."""

try:
    import xmlrpclib
except ImportError:
    xmlrpclib = None
    class XMLRPC: pass
else:
    from twisted.web import xmlrpc
    from twisted.web.xmlrpc import XMLRPC, addIntrospection

from twisted.trial import unittest
from twisted.web import server
from twisted.internet import reactor, defer
from twisted.python import log

import time

class Test(XMLRPC):

    FAILURE = 666
    NOT_FOUND = 23
    SESSION_EXPIRED = 42

    # the doc string is part of the test
    def xmlrpc_add(self, a, b):
        """This function add two numbers."""
        return a + b

    xmlrpc_add.signature = [['int', 'int', 'int'],
                            ['double', 'double', 'double']]

    # the doc string is part of the test
    def xmlrpc_pair(self, string, num):
        """This function puts the two arguments in an array."""
        return [string, num]

    xmlrpc_pair.signature = [['array', 'string', 'int']]

    # the doc string is part of the test
    def xmlrpc_defer(self, x):
        """Help for defer."""
        return defer.succeed(x)

    def xmlrpc_deferFail(self):
        return defer.fail(ValueError())

    # don't add a doc string, it's part of the test
    def xmlrpc_fail(self):
        raise RuntimeError

    def xmlrpc_fault(self):
        return xmlrpc.Fault(12, "hello")

    def xmlrpc_deferFault(self):
        return defer.fail(xmlrpc.Fault(17, "hi"))

    def xmlrpc_complex(self):
        return {"a": ["b", "c", 12, []], "D": "foo"}

    def xmlrpc_dict(self, map, key):
        return map[key]

    def _getFunction(self, functionPath):
        try:
            return XMLRPC._getFunction(self, functionPath)
        except xmlrpc.NoSuchFunction:
            if functionPath.startswith("SESSION"):
                raise xmlrpc.Fault(self.SESSION_EXPIRED, "Session non-existant/expired.")
            else:
                raise

    xmlrpc_dict.help = 'Help for dict.'


class XMLRPCTestCase(unittest.TestCase):

    def setUp(self):
        self.p = reactor.listenTCP(0, server.Site(Test()),
                                   interface="127.0.0.1")
        self.port = self.p.getHost().port

    def tearDown(self):
        self.p.stopListening()
        reactor.iterate()
        reactor.iterate()

    def proxy(self):
        return xmlrpc.Proxy("http://127.0.0.1:%d/" % self.port)

    def testResults(self):
        x = self.proxy().callRemote("add", 2, 3)
        self.assertEquals(unittest.deferredResult(x), 5)
        x = self.proxy().callRemote("defer", "a")
        self.assertEquals(unittest.deferredResult(x), "a")
        x = self.proxy().callRemote("dict", {"a" : 1}, "a")
        self.assertEquals(unittest.deferredResult(x), 1)
        x = self.proxy().callRemote("pair", 'a', 1)
        self.assertEquals(unittest.deferredResult(x), ['a', 1])
        x = self.proxy().callRemote("complex")
        self.assertEquals(unittest.deferredResult(x),
                          {"a": ["b", "c", 12, []], "D": "foo"})

    def testErrors(self):
        for code, methodName in [(666, "fail"), (666, "deferFail"),
                                 (12, "fault"), (23, "noSuchMethod"),
                                 (17, "deferFault"), (42, "SESSION_TEST")]:
            l = []
            d = self.proxy().callRemote(methodName).addErrback(l.append)
            timeout = time.time() + 10
            while not l and time.time() < timeout:
                reactor.iterate(0.01)
            if not l:
                self.fail("timeout")
            l[0].trap(xmlrpc.Fault)
            self.assertEquals(l[0].value.faultCode, code)
        log.flushErrors(RuntimeError, ValueError)


class XMLRPCTestCase2(XMLRPCTestCase):
    """Test with proxy that doesn't add a slash."""

    def proxy(self):
        return xmlrpc.Proxy("http://127.0.0.1:%d" % self.port)


class XMLRPCTestIntrospection(XMLRPCTestCase):

    def setUp(self):
        xmlrpc = Test()
        addIntrospection(xmlrpc)
        self.p = reactor.listenTCP(0, server.Site(xmlrpc),interface="127.0.0.1")
        self.port = self.p.getHost().port

    def testListMethods(self):
        d = self.proxy().callRemote("system.listMethods")
        list = unittest.deferredResult(d)
        list.sort()
        self.failUnlessEqual(list, ['add', 'complex', 'defer', 'deferFail',
                                    'deferFault', 'dict', 'fail', 'fault',
                                    'pair', 'system.listMethods',
                                    'system.methodHelp',
                                    'system.methodSignature'])

    def testMethodHelp(self):
        d = self.proxy().callRemote("system.methodHelp", 'defer')
        help = unittest.deferredResult(d)
        self.failUnlessEqual(help, 'Help for defer.')

        d = self.proxy().callRemote("system.methodHelp", 'fail')
        help = unittest.deferredResult(d)
        self.failUnlessEqual(help, '')

        d = self.proxy().callRemote("system.methodHelp", 'dict')
        help = unittest.deferredResult(d)
        self.failUnlessEqual(help, 'Help for dict.')

    def testMethodSignature(self):
        d = self.proxy().callRemote("system.methodSignature", 'defer')
        sig = unittest.deferredResult(d)
        self.failUnlessEqual(sig, '')

        d = self.proxy().callRemote("system.methodSignature", 'add')
        sig = unittest.deferredResult(d)
        self.failUnlessEqual(sig, [['int', 'int', 'int'],
                                   ['double', 'double', 'double']])

        d = self.proxy().callRemote("system.methodSignature", 'pair')
        sig = unittest.deferredResult(d)
        self.failUnlessEqual(sig, [['array', 'string', 'int']])
