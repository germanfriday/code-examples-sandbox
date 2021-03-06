
# Copyright (c) 2001-2004 Twisted Matrix Laboratories.
# See LICENSE for details.


"""
Test running processes.
"""

from twisted.trial import unittest
from twisted.test.test_process import SignalMixin

import gzip, os, popen2, time, sys, signal

# Twisted Imports
from twisted.internet import reactor, utils, interfaces
from twisted.python import components


class UtilsTestCase(SignalMixin, unittest.TestCase):
    """Test running a process."""

    output = None
    value = None

    def makeSourceFile(self, sourceLines):
        script = self.mktemp()
        scriptFile = file(script, 'wt')
        scriptFile.write(os.linesep.join(sourceLines) + os.linesep)
        scriptFile.close()
        return script

    def testOutput(self):
        scriptFile = self.makeSourceFile([
            'print "hello world"'
            ])
        d = utils.getProcessOutput(sys.executable, ['-u', scriptFile])
        return d.addCallback(self.assertEquals, "hello world\n")

    def testOutputWithErrorIgnored(self):
        # make sure stderr raises an error normally
        exe = sys.executable
        scriptFile = self.makeSourceFile([
            'import sys',
            'sys.stderr.write("hello world\\n")'
            ])

        d = utils.getProcessOutput(exe, ['-u', scriptFile], errortoo=0)
        return self.assertFailure(d, IOError)

    def testOutputWithErrorCollected(self):
        # make sure stderr raises an error normally
        exe = sys.executable
        scriptFile = self.makeSourceFile([
            'import sys',
            'sys.stderr.write("hello world\\n")'
            ])

        d = utils.getProcessOutput(exe, ['-u', scriptFile], errortoo=1)
        return d.addCallback(self.assertEquals, "hello world" + os.linesep)

    def testValue(self):
        exe = sys.executable
        scriptFile = self.makeSourceFile([
            "import sys",
            "sys.exit(1)"
            ])

        d = utils.getProcessValue(exe, ['-u', scriptFile])
        return d.addCallback(self.assertEquals, 1)

    def testOutputAndValue(self):
        exe = sys.executable
        scriptFile = self.makeSourceFile([
            "import sys",
            "sys.stdout.write('hello world!\\n')",
            "sys.stderr.write('goodbye world!\\n')",
            "sys.exit(1)"
            ])

        def gotOutputAndValue((out, err, code)):
            self.assertEquals(out, "hello world!" + os.linesep)
            self.assertEquals(err, "goodbye world!" + os.linesep)
            self.assertEquals(code, 1)
        d = utils.getProcessOutputAndValue(exe, [scriptFile])
        return d.addCallback(gotOutputAndValue)

    def testOutputSignal(self):
        # Use SIGKILL here because it's guaranteed to be delivered. Using
        # SIGHUP might not work in, e.g., a buildbot slave run under the
        # 'nohup' command.
        exe = sys.executable
        scriptFile = self.makeSourceFile([
            "import sys, os, signal",
            "sys.stdout.write('stdout bytes\\n')",
            "sys.stderr.write('stderr bytes\\n')",
            "sys.stdout.flush()",
            "sys.stderr.flush()",
            "os.kill(os.getpid(), signal.SIGKILL)"
            ])

        def gotOutputAndValue(err):
            (out, err, sig) = err.value # XXX Sigh wtf
            self.assertEquals(out, "stdout bytes" + os.linesep)
            self.assertEquals(err, "stderr bytes" + os.linesep)
            self.assertEquals(sig, signal.SIGKILL)

        d = utils.getProcessOutputAndValue(exe, ['-u', scriptFile])
        return d.addErrback(gotOutputAndValue)

if interfaces.IReactorProcess(reactor, None) is None:
    UtilsTestCase.skip = "reactor doesn't implement IReactorProcess"
