# -*- test-case-name: twisted.conch.test.test_insults -*-
# Copyright (c) 2001-2004 Twisted Matrix Laboratories.
# See LICENSE for details.

"""VT102 terminal manipulation

API Stability: Unstable

@author: U{Jp Calderone<mailto:exarkun@twistedmatrix.com>}
"""

import string

from zope.interface import implements

from twisted.internet import protocol, defer, interfaces as iinternet
from twisted.python import components

class ITerminalProtocol(components.Interface):
    def makeConnection(self, transport):
        """Called with an ITerminalTransport when a connection is established.
        """

    def keystrokeReceived(self, keyID, modifier):
        """A keystroke was received.

        Each keystroke corresponds to one invocation of this method.
        keyID is a string identifier for that key.  Printable characters
        are represented by themselves.  Control keys, such as arrows and
        function keys, are represented with symbolic constants on
        C{ServerProtocol}.
        """

    def terminalSize(self, width, height):
        """Called to indicate the size of the terminal.

        A terminal of 80x24 should be assumed if this method is not
        called.  This method might not be called for real terminals.
        """

    def unhandledControlSequence(self, seq):
        """Called when an unsupported control sequence is received.

        @type seq: C{str}
        @param seq: The whole control sequence which could not be interpreted.
        """

    def connectionLost(self, reason):
        """Called when the connection has been lost.

        reason is a Failure describing why.
        """

class TerminalProtocol(object):
    implements(ITerminalProtocol)

    def makeConnection(self, terminal):
        # assert ITerminalTransport.providedBy(transport), "TerminalProtocol.makeConnection must be passed an ITerminalTransport implementor"
        self.terminal = terminal
        self.connectionMade()

    def connectionMade(self):
        """Called after a connection has been established.
        """

    def keystrokeReceived(self, keyID, modifier):
        pass

    def terminalSize(self, width, height):
        pass

    def unhandledControlSequence(self, seq):
        pass

    def connectionLost(self, reason):
        pass

class ITerminalTransport(iinternet.ITransport):
    def cursorUp(self, n=1):
        """Move the cursor up n lines.
        """

    def cursorDown(self, n=1):
        """Move the cursor down n lines.
        """

    def cursorForward(self, n=1):
        """Move the cursor right n columns.
        """

    def cursorBackward(self, n=1):
        """Move the cursor left n columns.
        """

    def cursorPosition(self, column, line):
        """Move the cursor to the given line and column.
        """

    def cursorHome(self):
        """Move the cursor home.
        """

    def index(self):
        """Move the cursor down one line, performing scrolling if necessary.
        """

    def reverseIndex(self):
        """Move the cursor up one line, performing scrolling if necessary.
        """

    def nextLine(self):
        """Move the cursor to the first position on the next line, performing scrolling if necessary.
        """

    def saveCursor(self):
        """Save the cursor position, character attribute, character set, and origin mode selection.
        """

    def restoreCursor(self):
        """Restore the previously saved cursor position, character attribute, character set, and origin mode selection.

        If no cursor state was previously saved, move the cursor to the home position.
        """

    def setModes(self, modes):
        """Set the given modes on the terminal.
        """

    def resetModes(self, mode):
        """Reset the given modes on the terminal.
        """

    def applicationKeypadMode(self):
        """Cause keypad to generate control functions.

        Cursor key mode selects the type of characters generated by cursor keys.
        """

    def numericKeypadMode(self):
        """Cause keypad to generate normal characters.
        """

    def selectCharacterSet(self, charSet, which):
        """Select a character set.

        charSet should be one of CS_US, CS_UK, CS_DRAWING, CS_ALTERNATE, or
        CS_ALTERNATE_SPECIAL.

        which should be one of G0 or G1.
        """

    def shiftIn(self):
        """Activate the G0 character set.
        """

    def shiftOut(self):
        """Activate the G1 character set.
        """

    def singleShift2(self):
        """Shift to the G2 character set for a single character.
        """

    def singleShift3(self):
        """Shift to the G3 character set for a single character.
        """

    def selectGraphicRendition(self, *attributes):
        """Enabled one or more character attributes.

        Arguments should be one or more of UNDERLINE, REVERSE_VIDEO, BLINK, or BOLD.
        NORMAL may also be specified to disable all character attributes.
        """

    def horizontalTabulationSet(self):
        """Set a tab stop at the current cursor position.
        """

    def tabulationClear(self):
        """Clear the tab stop at the current cursor position.
        """

    def tabulationClearAll(self):
        """Clear all tab stops.
        """

    def doubleHeightLine(self, top=True):
        """Make the current line the top or bottom half of a double-height, double-width line.

        If top is True, the current line is the top half.  Otherwise, it is the bottom half.
        """

    def singleWidthLine(self):
        """Make the current line a single-width, single-height line.
        """

    def doubleWidthLine(self):
        """Make the current line a double-width line.
        """

    def eraseToLineEnd(self):
        """Erase from the cursor to the end of line, including cursor position.
        """

    def eraseToLineBeginning(self):
        """Erase from the cursor to the beginning of the line, including the cursor position.
        """

    def eraseLine(self):
        """Erase the entire cursor line.
        """

    def eraseToDisplayEnd(self):
        """Erase from the cursor to the end of the display, including the cursor position.
        """

    def eraseToDisplayBeginning(self):
        """Erase from the cursor to the beginning of the display, including the cursor position.
        """

    def eraseDisplay(self):
        """Erase the entire display.
        """

    def deleteCharacter(self, n=1):
        """Delete n characters starting at the cursor position.

        Characters to the right of deleted characters are shifted to the left.
        """

    def insertLine(self, n=1):
        """Insert n lines at the cursor position.

        Lines below the cursor are shifted down.  Lines moved past the bottom margin are lost.
        This command is ignored when the cursor is outside the scroll region.
        """

    def deleteLine(self, n=1):
        """Delete n lines starting at the cursor position.

        Lines below the cursor are shifted up.  This command is ignored when the cursor is outside
        the scroll region.
        """

    def reportCursorPosition(self):
        """Return a Deferred that fires with a two-tuple of (x, y) indicating the cursor position.
        """

    def reset(self):
        """Reset the terminal to its initial state.
        """

    def unhandledControlSequence(self, seq):
        """Called when an unsupported control sequence is received.

        @type seq: C{str}
        @param seq: The whole control sequence which could not be interpreted.
        """


CSI = '\x1b'
CST = {'~': 'tilde'}

class modes:
    """ECMA 48 standardized modes
    """

    # BREAKS YOPUR KEYBOARD MOFO
    KEYBOARD_ACTION = KAM = 2

    # When set, enables character insertion. New display characters
    # move old display characters to the right. Characters moved past
    # the right margin are lost.

    # When reset, enables replacement mode (disables character
    # insertion). New display characters replace old display
    # characters at cursor position. The old character is erased.
    INSERTION_REPLACEMENT = IRM = 4

    # Set causes a received linefeed, form feed, or vertical tab to
    # move cursor to first column of next line. RETURN transmits both
    # a carriage return and linefeed. This selection is also called
    # new line option.

    # Reset causes a received linefeed, form feed, or vertical tab to
    # move cursor to next line in current column. RETURN transmits a
    # carriage return.
    LINEFEED_NEWLINE = LNM = 20


class privateModes:
    """ANSI-Compatible Private Modes
    """
    ERROR = 0
    CURSOR_KEY = 1
    ANSI_VT52 = 2
    COLUMN = 3
    SCROLL = 4
    SCREEN = 5
    ORIGIN = 6
    AUTO_WRAP = 7
    AUTO_REPEAT = 8
    PRINTER_FORM_FEED = 18
    PRINTER_EXTENT = 19

    # Toggle cursor visibility (reset hides it)
    CURSOR_MODE = 25


# Character sets
CS_US = 'CS_US'
CS_UK = 'CS_UK'
CS_DRAWING = 'CS_DRAWING'
CS_ALTERNATE = 'CS_ALTERNATE'
CS_ALTERNATE_SPECIAL = 'CS_ALTERNATE_SPECIAL'

# Groupings (or something?? These are like variables that can be bound to character sets)
G0 = 'G0'
G1 = 'G1'

# G2 and G3 cannot be changed, but they can be shifted to.
G2 = 'G2'
G3 = 'G3'

# Character attributes

NORMAL = 0
BOLD = 1
UNDERLINE = 4
BLINK = 5
REVERSE_VIDEO = 7

class Vector:
    def __init__(self, x, y):
        self.x = x
        self.y = y

def log(s):
    file('log', 'a').write(str(s) + '\n')

# XXX TODO - These attributes are really part of the
# ITerminalTransport interface, I think.
_KEY_NAMES = ('UP_ARROW', 'DOWN_ARROW', 'RIGHT_ARROW', 'LEFT_ARROW',
              'HOME', 'INSERT', 'DELETE', 'END', 'PGUP', 'PGDN', 'NUMPAD_MIDDLE',
              'F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9',
              'F10', 'F11', 'F12',

              'ALT', 'SHIFT', 'CONTROL')

class _const(object):
    """
    @ivar name: A string naming this constant
    """
    def __init__(self, name):
        self.name = name

    def __repr__(self):
        return '[' + self.name + ']'


FUNCTION_KEYS = [
    _const(_name) for _name in _KEY_NAMES]

class ServerProtocol(protocol.Protocol):
    implements(ITerminalTransport)

    protocolFactory = None
    terminalProtocol = None

    TAB = '\t'
    BACKSPACE = '\x7f'
    ##

    lastWrite = ''

    state = 'data'

    termSize = Vector(80, 24)
    cursorPos = Vector(0, 0)
    scrollRegion = None

    # Factory who instantiated me
    factory = None

    def __init__(self, protocolFactory=None, *a, **kw):
        """
        @param protocolFactory: A callable which will be invoked with
        *a, **kw and should return an ITerminalProtocol implementor.
        This will be invoked when a connection to this ServerProtocol
        is established.

        @param a: Any positional arguments to pass to protocolFactory.
        @param kw: Any keyword arguments to pass to protocolFactory.
        """
        # assert protocolFactory is None or ITerminalProtocol.implementedBy(protocolFactory), "ServerProtocol.__init__ must be passed an ITerminalProtocol implementor"
        if protocolFactory is not None:
            self.protocolFactory = protocolFactory
        self.protocolArgs = a
        self.protocolKwArgs = kw

        self._cursorReports = []

    def connectionMade(self):
        if self.protocolFactory is not None:
            self.terminalProtocol = self.protocolFactory(*self.protocolArgs, **self.protocolKwArgs)

            try:
                factory = self.factory
            except AttributeError:
                pass
            else:
                self.terminalProtocol.factory = factory

            self.terminalProtocol.makeConnection(self)

    def dataReceived(self, data):
        for ch in data:
            if self.state == 'data':
                if ch == '\x1b':
                    self.state = 'escaped'
                else:
                    self.terminalProtocol.keystrokeReceived(ch, None)
            elif self.state == 'escaped':
                if ch == '[':
                    self.state = 'bracket-escaped'
                    self.escBuf = []
                elif ch == 'O':
                    self.state = 'low-function-escaped'
                else:
                    self.state = 'data'
                    self._handleShortControlSequence(ch)
            elif self.state == 'bracket-escaped':
                if ch == 'O':
                    self.state = 'low-function-escaped'
                elif ch.isalpha() or ch == '~':
                    self._handleControlSequence(''.join(self.escBuf) + ch)
                    del self.escBuf
                    self.state = 'data'
                else:
                    self.escBuf.append(ch)
            elif self.state == 'low-function-escaped':
                self._handleLowFunctionControlSequence(ch)
                self.state = 'data'
            else:
                raise ValueError("Illegal state")

    def _handleShortControlSequence(self, ch):
        self.terminalProtocol.keystrokeReceived(ch, self.ALT)

    def _handleControlSequence(self, buf):
        buf = '\x1b[' + buf
        f = getattr(self.controlSequenceParser, CST.get(buf[-1], buf[-1]), None)
        if f is None:
            self.unhandledControlSequence(buf)
        else:
            f(self, self.terminalProtocol, buf[:-1])

    def unhandledControlSequence(self, buf):
        self.terminalProtocol.unhandledControlSequence(buf)

    def _handleLowFunctionControlSequence(self, ch):
        map = {'P': self.F1, 'Q': self.F2, 'R': self.F3, 'S': self.F4}
        keyID = map.get(ch)
        if keyID is not None:
            self.terminalProtocol.keystrokeReceived(keyID, None)
        else:
            self.terminalProtocol.unhandledControlSequence('\x1b[O' + ch)

    class ControlSequenceParser:
        def A(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.UP_ARROW, None)
            else:
                handler.unhandledControlSequence(buf + 'A')

        def B(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.DOWN_ARROW, None)
            else:
                handler.unhandledControlSequence(buf + 'B')

        def C(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.RIGHT_ARROW, None)
            else:
                handler.unhandledControlSequence(buf + 'C')

        def D(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.LEFT_ARROW, None)
            else:
                handler.unhandledControlSequence(buf + 'D')

        def E(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.NUMPAD_MIDDLE, None)
            else:
                handler.unhandledControlSequence(buf + 'E')

        def F(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.END, None)
            else:
                handler.unhandledControlSequence(buf + 'F')

        def H(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.HOME, None)
            else:
                handler.unhandledControlSequence(buf + 'H')

        def R(self, proto, handler, buf):
            if not proto._cursorReports:
                handler.unhandledControlSequence(buf + 'R')
            elif buf.startswith('\x1b['):
                report = buf[2:]
                parts = report.split(';')
                if len(parts) != 2:
                    handler.unhandledControlSequence(buf + 'R')
                else:
                    Pl, Pc = parts
                    try:
                        Pl, Pc = int(Pl), int(Pc)
                    except ValueError:
                        handler.unhandledControlSequence(buf + 'R')
                    else:
                        d = proto._cursorReports.pop(0)
                        d.callback((Pc - 1, Pl - 1))
            else:
                handler.unhandledControlSequence(buf + 'R')

        def Z(self, proto, handler, buf):
            if buf == '\x1b[':
                handler.keystrokeReceived(proto.TAB, proto.SHIFT)
            else:
                handler.unhandledControlSequence(buf + 'Z')

        def tilde(self, proto, handler, buf):
            map = {1: proto.HOME, 2: proto.INSERT, 3: proto.DELETE,
                   4: proto.END,  5: proto.PGUP,   6: proto.PGDN,

                   15: proto.F5,  17: proto.F6, 18: proto.F7,
                   19: proto.F8,  20: proto.F9, 21: proto.F10,
                   23: proto.F11, 24: proto.F12}

            if buf.startswith('\x1b['):
                ch = buf[2:]
                try:
                    v = int(ch)
                except ValueError:
                    handler.unhandledControlSequence(buf + '~')
                else:
                    symbolic = map.get(v)
                    if symbolic is not None:
                        handler.keystrokeReceived(map[v], None)
                    else:
                        handler.unhandledControlSequence(buf + '~')
            else:
                handler.unhandledControlSequence(buf + '~')

    controlSequenceParser = ControlSequenceParser()

    # ITerminalTransport
    def cursorUp(self, n=1):
        self.cursorPos.y = max(self.cursorPos.y - n, 0)
        self.write('\x1b[%dA' % (n,))

    def cursorDown(self, n=1):
        self.cursorPos.y = min(self.cursorPos.y + n, self.termSize.y - 1)
        self.write('\x1b[%dB' % (n,))

    def cursorForward(self, n=1):
        self.cursorPos.x = min(self.cursorPos.x + n, self.termSize.x - 1)
        self.write('\x1b[%dC' % (n,))

    def cursorBackward(self, n=1):
        self.cursorPos.x = max(self.cursorPos.x - n, 0)
        self.write('\x1b[%dD' % (n,))

    def cursorPosition(self, column, line):
        self.write('\x1b[%d;%dH' % (line + 1, column + 1))

    def cursorHome(self):
        self.cursorPos.x = self.cursorPos.y = 0
        self.write('\x1b[H')

    def index(self):
        self.cursorPos.y = min(self.cursorPos.y + 1, self.termSize.y - 1)
        self.write('\x1bD')

    def reverseIndex(self):
        self.cursorPos.y = max(self.cursorPos.y - 1, 0)
        self.write('\x1bM')

    def nextLine(self):
        self.cursorPos.x = 0
        self.cursorPos.y = min(self.cursorPos.y + 1, self.termSize.y - 1)
        self.write('\x1bE')

    def saveCursor(self):
        self._savedCursorPos = Vector(self.cursorPos.x, self.cursorPos.y)
        self.write('\x1b7')

    def restoreCursor(self):
        self.cursorPos = self._savedCursorPos
        del self._savedCursorPos
        self.write('\x1b8')

    def setModes(self, modes):
        # XXX Support ANSI-Compatible private modes
        self.write('\x1b[%sh' % (';'.join(map(str, modes)),))

    def setPrivateModes(self, modes):
        self.write('\x1b[?%sh' % (';'.join(map(str, modes)),))

    def resetModes(self, modes):
        # XXX Support ANSI-Compatible private modes
        self.write('\x1b[%sl' % (';'.join(map(str, modes)),))

    def resetPrivateModes(self, modes):
        self.write('\x1b[?%sl' % (';'.join(map(str, modes)),))

    def applicationKeypadMode(self):
        self.write('\x1b=')

    def numericKeypadMode(self):
        self.write('\x1b>')

    def selectCharacterSet(self, charSet, which):
        # XXX Rewrite these as dict lookups
        if which == G0:
            which = '('
        elif which == G1:
            which = ')'
        else:
            raise ValueError("`which' argument to selectCharacterSet must be G0 or G1")
        if charSet == CS_UK:
            charSet = 'A'
        elif charSet == CS_US:
            charSet = 'B'
        elif charSet == CS_DRAWING:
            charSet = '0'
        elif charSet == CS_ALTERNATE:
            charSet = '1'
        elif charSet == CS_ALTERNATE_SPECIAL:
            charSet = '2'
        else:
            raise ValueError("Invalid `charSet' argument to selectCharacterSet")
        self.write('\x1b' + which + charSet)

    def shiftIn(self):
        self.write('\x15')

    def shiftOut(self):
        self.write('\x14')

    def singleShift2(self):
        self.write('\x1bN')

    def singleShift3(self):
        self.write('\x1bO')

    def selectGraphicRendition(self, *attributes):
        attrs = []
        for a in attributes:
            attrs.append(a)
        self.write('\x1b[%sm' % (';'.join(attrs),))

    def horizontalTabulationSet(self):
        self.write('\x1bH')

    def tabulationClear(self):
        self.write('\x1b[q')

    def tabulationClearAll(self):
        self.write('\x1b[3q')

    def doubleHeightLine(self, top=True):
        if top:
            self.write('\x1b#3')
        else:
            self.write('\x1b#4')

    def singleWidthLine(self):
        self.write('\x1b#5')

    def doubleWidthLine(self):
        self.write('\x1b#6')

    def eraseToLineEnd(self):
        self.write('\x1b[K')

    def eraseToLineBeginning(self):
        self.write('\x1b[1K')

    def eraseLine(self):
        self.write('\x1b[2K')

    def eraseToDisplayEnd(self):
        self.write('\x1b[J')

    def eraseToDisplayBeginning(self):
        self.write('\x1b[1J')

    def eraseDisplay(self):
        self.write('\x1b[2J')

    def deleteCharacter(self, n=1):
        self.write('\x1b[%dP' % (n,))

    def insertLine(self, n=1):
        self.write('\x1b[%dL' % (n,))

    def deleteLine(self, n=1):
        self.write('\x1b[%dM' % (n,))

    def setScrollRegion(self, first=None, last=None):
        if first is not None:
            first = '%d' % (first,)
        else:
            first = ''
        if last is not None:
            last = '%d' % (last,)
        else:
            last = ''
        self.write('\x1b[%s;%sr' % (first, last))

    def resetScrollRegion(self):
        self.setScrollRegion()

    def reportCursorPosition(self):
        d = defer.Deferred()
        self._cursorReports.append(d)
        self.write('\x1b[6n')
        return d

    def reset(self):
        self.cursorPos.x = self.cursorPos.y = 0
        try:
            del self._savedCursorPos
        except AttributeError:
            pass
        self.write('\x1bc')

    # ITransport
    def write(self, bytes):
        if bytes:
            self.lastWrite = bytes
            self.transport.write('\r\n'.join(bytes.split('\n')))

    def writeSequence(self, bytes):
        self.write(''.join(bytes))

    def loseConnection(self):
        self.reset()
        self.transport.loseConnection()

    def connectionLost(self, reason):
        if self.terminalProtocol is not None:
            try:
                self.terminalProtocol.connectionLost(reason)
            finally:
                self.terminalProtocol = None
# Add symbolic names for function keys
for name, const in zip(_KEY_NAMES, FUNCTION_KEYS):
    setattr(ServerProtocol, name, const)



class ClientProtocol(protocol.Protocol):

    terminalFactory = None
    terminal = None

    state = 'data'

    _escBuf = None

    _shorts = {
        'D': 'index',
        'M': 'reverseIndex',
        'E': 'nextLine',
        '7': 'saveCursor',
        '8': 'restoreCursor',
        '=': 'applicationKeypadMode',
        '>': 'numericKeypadMode',
        'N': 'singleShift2',
        'O': 'singleShift3',
        'H': 'horizontalTabulationSet',
        'c': 'reset'}

    _longs = {
        '[': 'bracket-escape',
        '(': 'select-g0',
        ')': 'select-g1',
        '#': 'select-height-width'}

    _charsets = {
        'A': CS_UK,
        'B': CS_US,
        '0': CS_DRAWING,
        '1': CS_ALTERNATE,
        '2': CS_ALTERNATE_SPECIAL}

    # Factory who instantiated me
    factory = None

    def __init__(self, terminalFactory=None, *a, **kw):
        """
        @param terminalFactory: A callable which will be invoked with
        *a, **kw and should return an ITerminalTransport provider.
        This will be invoked when this ClientProtocol establishes a
        connection.

        @param a: Any positional arguments to pass to terminalFactory.
        @param kw: Any keyword arguments to pass to terminalFactory.
        """
        # assert terminalFactory is None or ITerminalTransport.implementedBy(terminalFactory), "ClientProtocol.__init__ must be passed an ITerminalTransport implementor"
        if terminalFactory is not None:
            self.terminalFactory = terminalFactory
        self.terminalArgs = a
        self.terminalKwArgs = kw

    def connectionMade(self):
        if self.terminalFactory is not None:
            self.terminal = self.terminalFactory(*self.terminalArgs, **self.terminalKwArgs)
            self.terminal.factory = self.factory
            self.terminal.makeConnection(self)

    def connectionLost(self, reason):
        if self.terminal is not None:
            try:
                self.terminal.connectionLost(reason)
            finally:
                del self.terminal

    def dataReceived(self, bytes):
        for b in bytes:
            if self.state == 'data':
                if b == '\x1b':
                    self.state = 'escaped'
                elif b == '\x14':
                    self.terminal.shiftOut()
                elif b == '\x15':
                    self.terminal.shiftIn()
                elif b == '\x08':
                    self.terminal.cursorBackward()
                else:
                    self.terminal.write(b)
            elif self.state == 'escaped':
                fName = self._shorts.get(b)
                if fName is not None:
                    self.state = 'data'
                    getattr(self.terminal, fName)()
                else:
                    state = self._longs.get(b)
                    if state is not None:
                        self.state = state
                    else:
                        self.terminal.unhandledControlSequence('\x1b' + b)
                        self.state = 'data'
            elif self.state == 'bracket-escape':
                if self._escBuf is None:
                    self._escBuf = []
                if b.isalpha() or b == '~':
                    self._handleControlSequence(''.join(self._escBuf), b)
                    del self._escBuf
                    self.state = 'data'
                else:
                    self._escBuf.append(b)
            elif self.state == 'select-g0':
                self.terminal.selectCharacterSet(self._charsets.get(b, b), G0)
                self.state = 'data'
            elif self.state == 'select-g1':
                self.terminal.selectCharacterSet(self._charsets.get(b, b), G1)
                self.state = 'data'
            elif self.state == 'select-height-width':
                self._handleHeightWidth(b)
                self.state = 'data'
            else:
                raise ValueError("Illegal state")

    def _handleControlSequence(self, buf, terminal):
        f = getattr(self.controlSequenceParser, CST.get(terminal, terminal), None)
        if f is None:
            self.terminal.unhandledControlSequence('\x1b[' + buf + terminal)
        else:
            f(self, self.terminal, buf)

    class ControlSequenceParser:
        def _makeSimple(ch, fName):
            n = 'cursor' + fName
            def simple(self, proto, handler, buf):
                if not buf:
                    getattr(handler, n)(1)
                else:
                    try:
                        m = int(buf)
                    except ValueError:
                        handler.unhandledControlSequence('\x1b[' + buf + ch)
                    else:
                        getattr(handler, n)(m)
            return simple
        for (ch, fName) in (('A', 'Up'),
                            ('B', 'Down'),
                            ('C', 'Forward'),
                            ('D', 'Backward')):
            exec ch + " = _makeSimple(ch, fName)"
        del _makeSimple

        def h(self, proto, handler, buf):
            # XXX - Handle '?' to introduce ANSI-Compatible private modes.
            try:
                modes = map(int, buf.split(';'))
            except ValueError:
                handler.unhandledControlSequence('\x1b[' + buf + 'h')
            else:
                handler.setModes(modes)

        def l(self, proto, handler, buf):
            # XXX - Handle '?' to introduce ANSI-Compatible private modes.
            try:
                modes = map(int, buf.split(';'))
            except ValueError:
                handler.unhandledControlSequence('\x1b[' + buf + 'l')
            else:
                handler.resetModes(modes)

        def r(self, proto, handler, buf):
            parts = buf.split(';')
            if len(parts) == 1:
                handler.setScrollRegion(None, None)
            elif len(parts) == 2:
                try:
                    if parts[0]:
                        pt = int(parts[0])
                    else:
                        pt = None
                    if parts[1]:
                        pb = int(parts[1])
                    else:
                        pb = None
                except ValueError:
                    handler.unhandledControlSequence('\x1b[' + buf + 'r')
                else:
                    handler.setScrollRegion(pt, pb)
            else:
                handler.unhandledControlSequence('\x1b[' + buf + 'r')

        def K(self, proto, handler, buf):
            if not buf:
                handler.eraseToLineEnd()
            elif buf == '1':
                handler.eraseToLineBeginning()
            elif buf == '2':
                handler.eraseLine()
            else:
                handler.unhandledControlSequence('\x1b[' + buf + 'K')

        def H(self, proto, handler, buf):
            handler.cursorHome()

        def J(self, proto, handler, buf):
            if not buf:
                handler.eraseToDisplayEnd()
            elif buf == '1':
                handler.eraseToDisplayBeginning()
            elif buf == '2':
                handler.eraseDisplay()
            else:
                handler.unhandledControlSequence('\x1b[' + buf + 'J')

        def P(self, proto, handler, buf):
            if not buf:
                handler.deleteCharacter(1)
            else:
                try:
                    n = int(buf)
                except ValueError:
                    handler.unhandledControlSequence('\x1b[' + buf + 'P')
                else:
                    handler.deleteCharacter(n)

        def L(self, proto, handler, buf):
            if not buf:
                handler.insertLine(1)
            else:
                try:
                    n = int(buf)
                except ValueError:
                    handler.unhandledControlSequence('\x1b[' + buf + 'L')
                else:
                    handler.insertLine(n)

        def M(self, proto, handler, buf):
            if not buf:
                handler.deleteLine(1)
            else:
                try:
                    n = int(buf)
                except ValueError:
                    handler.unhandledControlSequence('\x1b[' + buf + 'M')
                else:
                    handler.deleteLine(n)

        def n(self, proto, handler, buf):
            if buf == '6':
                x, y = handler.reportCursorPosition()
                proto.transport.write('\x1b[%d;%dR' % (x + 1, y + 1))
            else:
                handler.unhandledControlSequence('\x1b[' + buf + 'n')

        def m(self, proto, handler, buf):
            if not buf:
                handler.selectGraphicRendition(NORMAL)
            else:
                attrs = []
                for a in buf.split(';'):
                    try:
                        a = int(a)
                    except ValueError:
                        pass
                    attrs.append(a)
                handler.selectGraphicRendition(*attrs)

    controlSequenceParser = ControlSequenceParser()

    def _handleHeightWidth(self, b):
        if b == '3':
            self.terminal.doubleHeightLine(True)
        elif b == '4':
            self.terminal.doubleHeightLine(False)
        elif b == '5':
            self.terminal.singleWidthLine()
        elif b == '6':
            self.terminal.doubleWidthLine()
        else:
            self.terminal.unhandledControlSequence('\x1b#' + b)


__all__ = [
    # Interfaces
    'ITerminalProtocol', 'ITerminalTransport',

    # Symbolic constants
    'modes', 'privateModes', 'FUNCTION_KEYS',

    'CS_US', 'CS_UK', 'CS_DRAWING', 'CS_ALTERNATE', 'CS_ALTERNATE_SPECIAL',
    'G0', 'G1', 'G2', 'G3',

    'UNDERLINE', 'REVERSE_VIDEO', 'BLINK', 'BOLD', 'NORMAL',

    # Protocol classes
    'ServerProtocol', 'ClientProtocol']
