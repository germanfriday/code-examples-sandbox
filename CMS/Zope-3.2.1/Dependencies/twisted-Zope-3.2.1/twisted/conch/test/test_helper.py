# -*- test-case-name: twisted.conch.test.test_helper -*-
# Copyright (c) 2001-2004 Twisted Matrix Laboratories.
# See LICENSE for details.

from twisted.conch.insults import helper
from twisted.conch.insults.insults import ServerProtocol, ClientProtocol
from twisted.conch.insults.insults import G0, G1, G2, G3
from twisted.conch.insults.insults import modes
from twisted.conch.insults.insults import NORMAL, BOLD, UNDERLINE, BLINK, REVERSE_VIDEO

from twisted.trial import unittest

WIDTH = 80
HEIGHT = 24

class BufferTestCase(unittest.TestCase):
    def setUp(self):
        self.term = helper.TerminalBuffer()
        self.term.connectionMade()

    def testInitialState(self):
        self.assertEquals(self.term.width, WIDTH)
        self.assertEquals(self.term.height, HEIGHT)
        self.assertEquals(str(self.term),
                          '\n' * (HEIGHT - 1))
        self.assertEquals(self.term.reportCursorPosition(), (0, 0))

    def testCursorDown(self):
        self.term.cursorDown(3)
        self.assertEquals(self.term.reportCursorPosition(), (0, 3))
        self.term.cursorDown()
        self.assertEquals(self.term.reportCursorPosition(), (0, 4))
        self.term.cursorDown(HEIGHT)
        self.assertEquals(self.term.reportCursorPosition(), (0, HEIGHT - 1))

    def testCursorUp(self):
        self.term.cursorUp(5)
        self.assertEquals(self.term.reportCursorPosition(), (0, 0))

        self.term.cursorDown(20)
        self.term.cursorUp(1)
        self.assertEquals(self.term.reportCursorPosition(), (0, 19))

        self.term.cursorUp(19)
        self.assertEquals(self.term.reportCursorPosition(), (0, 0))

    def testCursorForward(self):
        self.term.cursorForward(2)
        self.assertEquals(self.term.reportCursorPosition(), (2, 0))
        self.term.cursorForward(2)
        self.assertEquals(self.term.reportCursorPosition(), (4, 0))
        self.term.cursorForward(WIDTH)
        self.assertEquals(self.term.reportCursorPosition(), (WIDTH, 0))

    def testCursorBackward(self):
        self.term.cursorForward(10)
        self.term.cursorBackward(2)
        self.assertEquals(self.term.reportCursorPosition(), (8, 0))
        self.term.cursorBackward(7)
        self.assertEquals(self.term.reportCursorPosition(), (1, 0))
        self.term.cursorBackward(1)
        self.assertEquals(self.term.reportCursorPosition(), (0, 0))
        self.term.cursorBackward(1)
        self.assertEquals(self.term.reportCursorPosition(), (0, 0))

    def testCursorPositioning(self):
        self.term.cursorPosition(3, 9)
        self.assertEquals(self.term.reportCursorPosition(), (3, 9))

    def testSimpleWriting(self):
        s = "Hello, world."
        self.term.write(s)
        self.assertEquals(
            str(self.term),
            s + '\n' +
            '\n' * (HEIGHT - 2))

    def testOvertype(self):
        s = "hello, world."
        self.term.write(s)
        self.term.cursorBackward(len(s))
        self.term.resetModes([modes.IRM])
        self.term.write("H")
        self.assertEquals(
            str(self.term),
            ("H" + s[1:]) + '\n' +
            '\n' * (HEIGHT - 2))

    def testInsert(self):
        s = "ello, world."
        self.term.write(s)
        self.term.cursorBackward(len(s))
        self.term.setModes([modes.IRM])
        self.term.write("H")
        self.assertEquals(
            str(self.term),
            ("H" + s) + '\n' +
            '\n' * (HEIGHT - 2))

    def testWritingInTheMiddle(self):
        s = "Hello, world."
        self.term.cursorDown(5)
        self.term.cursorForward(5)
        self.term.write(s)
        self.assertEquals(
            str(self.term),
            '\n' * 5 +
            (self.term.fill * 5) + s + '\n' +
            '\n' * (HEIGHT - 7))

    def testWritingWrappedAtEndOfLine(self):
        s = "Hello, world."
        self.term.cursorForward(WIDTH - 5)
        self.term.write(s)
        self.assertEquals(
            str(self.term),
            s[:5].rjust(WIDTH) + '\n' +
            s[5:] + '\n' +
            '\n' * (HEIGHT - 3))

    def testIndex(self):
        self.term.index()
        self.assertEquals(self.term.reportCursorPosition(), (0, 1))
        self.term.cursorDown(HEIGHT)
        self.assertEquals(self.term.reportCursorPosition(), (0, HEIGHT - 1))
        self.term.index()
        self.assertEquals(self.term.reportCursorPosition(), (0, HEIGHT - 1))

    def testReverseIndex(self):
        self.term.reverseIndex()
        self.assertEquals(self.term.reportCursorPosition(), (0, 0))
        self.term.cursorDown(2)
        self.assertEquals(self.term.reportCursorPosition(), (0, 2))
        self.term.reverseIndex()
        self.assertEquals(self.term.reportCursorPosition(), (0, 1))

    def testNextLine(self):
        self.term.nextLine()
        self.assertEquals(self.term.reportCursorPosition(), (0, 1))
        self.term.cursorForward(5)
        self.assertEquals(self.term.reportCursorPosition(), (5, 1))
        self.term.nextLine()
        self.assertEquals(self.term.reportCursorPosition(), (0, 2))

    def testSaveCursor(self):
        self.term.cursorDown(5)
        self.term.cursorForward(7)
        self.assertEquals(self.term.reportCursorPosition(), (7, 5))
        self.term.saveCursor()
        self.term.cursorDown(7)
        self.term.cursorBackward(3)
        self.assertEquals(self.term.reportCursorPosition(), (4, 12))
        self.term.restoreCursor()
        self.assertEquals(self.term.reportCursorPosition(), (7, 5))

    def testSingleShifts(self):
        self.term.singleShift2()
        self.term.write('Hi')

        ch = self.term.getCharacter(0, 0)
        self.assertEquals(ch[0], 'H')
        self.assertEquals(ch[1].charset, G2)

        ch = self.term.getCharacter(1, 0)
        self.assertEquals(ch[0], 'i')
        self.assertEquals(ch[1].charset, G0)

        self.term.singleShift3()
        self.term.write('!!')

        ch = self.term.getCharacter(2, 0)
        self.assertEquals(ch[0], '!')
        self.assertEquals(ch[1].charset, G3)

        ch = self.term.getCharacter(3, 0)
        self.assertEquals(ch[0], '!')
        self.assertEquals(ch[1].charset, G0)

    def testShifting(self):
        s1 = "Hello"
        s2 = "World"
        s3 = "Bye!"
        self.term.write("Hello\n")
        self.term.shiftOut()
        self.term.write("World\n")
        self.term.shiftIn()
        self.term.write("Bye!\n")

        g = G0
        h = 0
        for s in (s1, s2, s3):
            for i in range(len(s)):
                ch = self.term.getCharacter(i, h)
                self.assertEquals(ch[0], s[i])
                self.assertEquals(ch[1].charset, g)
            g = g == G0 and G1 or G0
            h += 1

    def testGraphicRendition(self):
        self.term.selectGraphicRendition(BOLD, UNDERLINE, BLINK, REVERSE_VIDEO)
        self.term.write('W')
        self.term.selectGraphicRendition(NORMAL)
        self.term.write('X')
        self.term.selectGraphicRendition(BLINK)
        self.term.write('Y')
        self.term.selectGraphicRendition(BOLD)
        self.term.write('Z')

        ch = self.term.getCharacter(0, 0)
        self.assertEquals(ch[0], 'W')
        self.failUnless(ch[1].bold)
        self.failUnless(ch[1].underline)
        self.failUnless(ch[1].blink)
        self.failUnless(ch[1].reverseVideo)

        ch = self.term.getCharacter(1, 0)
        self.assertEquals(ch[0], 'X')
        self.failIf(ch[1].bold)
        self.failIf(ch[1].underline)
        self.failIf(ch[1].blink)
        self.failIf(ch[1].reverseVideo)

        ch = self.term.getCharacter(2, 0)
        self.assertEquals(ch[0], 'Y')
        self.failUnless(ch[1].blink)
        self.failIf(ch[1].bold)
        self.failIf(ch[1].underline)
        self.failIf(ch[1].reverseVideo)

        ch = self.term.getCharacter(3, 0)
        self.assertEquals(ch[0], 'Z')
        self.failUnless(ch[1].blink)
        self.failUnless(ch[1].bold)
        self.failIf(ch[1].underline)
        self.failIf(ch[1].reverseVideo)

    def testColorAttributes(self):
        s1 = "Merry xmas"
        s2 = "Just kidding"
        self.term.selectGraphicRendition(helper.FOREGROUND + helper.RED,
                                         helper.BACKGROUND + helper.GREEN)
        self.term.write(s1 + "\n")
        self.term.selectGraphicRendition(NORMAL)
        self.term.write(s2 + "\n")

        for i in range(len(s1)):
            ch = self.term.getCharacter(i, 0)
            self.assertEquals(ch[0], s1[i])
            self.assertEquals(ch[1].charset, G0)
            self.assertEquals(ch[1].bold, False)
            self.assertEquals(ch[1].underline, False)
            self.assertEquals(ch[1].blink, False)
            self.assertEquals(ch[1].reverseVideo, False)
            self.assertEquals(ch[1].foreground, helper.RED)
            self.assertEquals(ch[1].background, helper.GREEN)

        for i in range(len(s2)):
            ch = self.term.getCharacter(i, 1)
            self.assertEquals(ch[0], s2[i])
            self.assertEquals(ch[1].charset, G0)
            self.assertEquals(ch[1].bold, False)
            self.assertEquals(ch[1].underline, False)
            self.assertEquals(ch[1].blink, False)
            self.assertEquals(ch[1].reverseVideo, False)
            self.assertEquals(ch[1].foreground, helper.WHITE)
            self.assertEquals(ch[1].background, helper.BLACK)

    def testEraseLine(self):
        s1 = 'line 1'
        s2 = 'line 2'
        s3 = 'line 3'
        self.term.write('\n'.join((s1, s2, s3)) + '\n')
        self.term.cursorPosition(1, 1)
        self.term.eraseLine()

        self.assertEquals(
            str(self.term),
            s1 + '\n' +
            '\n' +
            s3 + '\n' +
            '\n' * (HEIGHT - 4))

    def testEraseToLineEnd(self):
        s = 'Hello, world.'
        self.term.write(s)
        self.term.cursorBackward(5)
        self.term.eraseToLineEnd()
        self.assertEquals(
            str(self.term),
            s[:-5] + '\n' +
            '\n' * (HEIGHT - 2))

    def testEraseToLineBeginning(self):
        s = 'Hello, world.'
        self.term.write(s)
        self.term.cursorBackward(5)
        self.term.eraseToLineBeginning()
        self.assertEquals(
            str(self.term),
            s[-4:].rjust(len(s)) + '\n' +
            '\n' * (HEIGHT - 2))

    def testEraseDisplay(self):
        self.term.write('Hello world\n')
        self.term.write('Goodbye world\n')
        self.term.eraseDisplay()

        self.assertEquals(
            str(self.term),
            '\n' * (HEIGHT - 1))

    def testEraseToDisplayEnd(self):
        s1 = "Hello world"
        s2 = "Goodbye world"
        self.term.write('\n'.join((s1, s2, '')))
        self.term.cursorPosition(5, 1)
        self.term.eraseToDisplayEnd()

        self.assertEquals(
            str(self.term),
            s1 + '\n' +
            s2[:5] + '\n' +
            '\n' * (HEIGHT - 3))

    def testEraseToDisplayBeginning(self):
        s1 = "Hello world"
        s2 = "Goodbye world"
        self.term.write('\n'.join((s1, s2)))
        self.term.cursorPosition(5, 1)
        self.term.eraseToDisplayBeginning()

        self.assertEquals(
            str(self.term),
            '\n' +
            s2[6:].rjust(len(s2)) + '\n' +
            '\n' * (HEIGHT - 3))

    def testLineInsertion(self):
        s1 = "Hello world"
        s2 = "Goodbye world"
        self.term.write('\n'.join((s1, s2)))
        self.term.cursorPosition(7, 1)
        self.term.insertLine()

        self.assertEquals(
            str(self.term),
            s1 + '\n' +
            '\n' +
            s2 + '\n' +
            '\n' * (HEIGHT - 4))

    def testLineDeletion(self):
        s1 = "Hello world"
        s2 = "Middle words"
        s3 = "Goodbye world"
        self.term.write('\n'.join((s1, s2, s3)))
        self.term.cursorPosition(9, 1)
        self.term.deleteLine()

        self.assertEquals(
            str(self.term),
            s1 + '\n' +
            s3 + '\n' +
            '\n' * (HEIGHT - 3))

class ExpectTestCase(unittest.TestCase):
    def setUp(self):
        self.term = helper.ExpectableBuffer()
        self.term.connectionMade()

    def testSimpleString(self):
        result = []
        d = self.term.expect("hello world")
        d.addCallback(result.append)

        self.term.write("greeting puny earthlings\n")
        self.failIf(result)
        self.term.write("hello world\n")
        self.failUnless(result)
        self.assertEquals(result[0].group(), "hello world")

    def testBrokenUpString(self):
        result = []
        d = self.term.expect("hello world")
        d.addCallback(result.append)

        self.failIf(result)
        self.term.write("hello ")
        self.failIf(result)
        self.term.write("worl")
        self.failIf(result)
        self.term.write("d")
        self.failUnless(result)
        self.assertEquals(result[0].group(), "hello world")


    def testMultiple(self):
        result = []
        d1 = self.term.expect("hello ")
        d1.addCallback(result.append)
        d2 = self.term.expect("world")
        d2.addCallback(result.append)

        self.failIf(result)
        self.term.write("hello")
        self.failIf(result)
        self.term.write(" ")
        self.assertEquals(len(result), 1)
        self.term.write("world")
        self.assertEquals(len(result), 2)
        self.assertEquals(result[0].group(), "hello ")
        self.assertEquals(result[1].group(), "world")

    def testSynchronous(self):
        self.term.write("hello world")

        result = []
        d = self.term.expect("hello world")
        d.addCallback(result.append)
        self.failUnless(result)
        self.assertEquals(result[0].group(), "hello world")

    def testMultipleSynchronous(self):
        self.term.write("goodbye world")

        result = []
        d1 = self.term.expect("bye")
        d1.addCallback(result.append)
        d2 = self.term.expect("world")
        d2.addCallback(result.append)

        self.assertEquals(len(result), 2)
        self.assertEquals(result[0].group(), "bye")
        self.assertEquals(result[1].group(), "world")
