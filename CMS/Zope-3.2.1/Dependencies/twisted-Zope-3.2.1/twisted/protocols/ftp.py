# -*- test-case-name: twisted.test.test_ftp -*-
# Copyright (c) 2001-2004 Twisted Matrix Laboratories.
# See LICENSE for details.

"""
An FTP protocol implementation

@author: U{Itamar Shtull-Trauring<mailto:itamarst@twistedmatrix.com>}
@author: U{Jp Calderone<mailto:exarkun@divmod.com>}
@author: U{Andrew Bennetts<mailto:spiv@twistedmatrix.com>}

API stability: FTPClient is stable, FTP and FTPFactory (server) is unstable.
"""

# System Imports
import os
import time
import re
import operator
import stat
import errno
import fnmatch

try:
    import pwd, grp
except ImportError:
    pwd = grp = None

from zope.interface import Interface, implements

# Twisted Imports
from twisted import copyright
from twisted.internet import reactor, interfaces, protocol, error, defer
from twisted.protocols import basic, policies

from twisted.python import log, components, failure, filepath

from twisted.cred import error as cred_error, portal, credentials, checkers

# constants
# response codes

RESTART_MARKER_REPLY                    = "100"
SERVICE_READY_IN_N_MINUTES              = "120"
DATA_CNX_ALREADY_OPEN_START_XFR         = "125"
FILE_STATUS_OK_OPEN_DATA_CNX            = "150"

CMD_OK                                  = "200.1"
TYPE_SET_OK                             = "200.2"
ENTERING_PORT_MODE                      = "200.3"
CMD_NOT_IMPLMNTD_SUPERFLUOUS            = "202"
SYS_STATUS_OR_HELP_REPLY                = "211"
DIR_STATUS                              = "212"
FILE_STATUS                             = "213"
HELP_MSG                                = "214"
NAME_SYS_TYPE                           = "215"
SVC_READY_FOR_NEW_USER                  = "220.1"
WELCOME_MSG                             = "220.2"
SVC_CLOSING_CTRL_CNX                    = "221"
GOODBYE_MSG                             = "221"
DATA_CNX_OPEN_NO_XFR_IN_PROGRESS        = "225"
CLOSING_DATA_CNX                        = "226"
TXFR_COMPLETE_OK                        = "226"
ENTERING_PASV_MODE                      = "227"
ENTERING_EPSV_MODE                      = "229"
USR_LOGGED_IN_PROCEED                   = "230.1"     # v1 of code 230
GUEST_LOGGED_IN_PROCEED                 = "230.2"     # v2 of code 230
REQ_FILE_ACTN_COMPLETED_OK              = "250"
PWD_REPLY                               = "257.1"
MKD_REPLY                               = "257.2"

USR_NAME_OK_NEED_PASS                   = "331.1"     # v1 of Code 331
GUEST_NAME_OK_NEED_EMAIL                = "331.2"     # v2 of code 331
NEED_ACCT_FOR_LOGIN                     = "332"
REQ_FILE_ACTN_PENDING_FURTHER_INFO      = "350"

SVC_NOT_AVAIL_CLOSING_CTRL_CNX          = "421.1"
TOO_MANY_CONNECTIONS                    = "421.2"
CANT_OPEN_DATA_CNX                      = "425"
CNX_CLOSED_TXFR_ABORTED                 = "426"
REQ_ACTN_ABRTD_FILE_UNAVAIL             = "450"
REQ_ACTN_ABRTD_LOCAL_ERR                = "451"
REQ_ACTN_ABRTD_INSUFF_STORAGE           = "452"

SYNTAX_ERR                              = "500"
SYNTAX_ERR_IN_ARGS                      = "501"
CMD_NOT_IMPLMNTD                        = "502"
BAD_CMD_SEQ                             = "503"
CMD_NOT_IMPLMNTD_FOR_PARAM              = "504"
NOT_LOGGED_IN                           = "530.1"     # v1 of code 530 - please log in
AUTH_FAILURE                            = "530.2"     # v2 of code 530 - authorization failure
NEED_ACCT_FOR_STOR                      = "532"
FILE_NOT_FOUND                          = "550.1"     # no such file or directory
PERMISSION_DENIED                       = "550.2"     # permission denied
ANON_USER_DENIED                        = "550.3"     # anonymous users can't alter filesystem
IS_NOT_A_DIR                            = "550.4"     # rmd called on a path that is not a directory
REQ_ACTN_NOT_TAKEN                      = "550.5"
PAGE_TYPE_UNK                           = "551"
EXCEEDED_STORAGE_ALLOC                  = "552"
FILENAME_NOT_ALLOWED                    = "553"


RESPONSE = {
    # -- 100's --
    RESTART_MARKER_REPLY:               '110 MARK yyyy-mmmm', # TODO: this must be fixed
    SERVICE_READY_IN_N_MINUTES:         '120 service ready in %s minutes',
    DATA_CNX_ALREADY_OPEN_START_XFR:    '125 Data connection already open, starting transfer',
    FILE_STATUS_OK_OPEN_DATA_CNX:       '150 File status okay; about to open data connection.',

    # -- 200's --
    CMD_OK:                             '200 Command OK',
    TYPE_SET_OK:                        '200 Type set to %s.',
    ENTERING_PORT_MODE:                 '200 PORT OK',
    CMD_NOT_IMPLMNTD_SUPERFLUOUS:       '202 Command not implemented, superfluous at this site',
    SYS_STATUS_OR_HELP_REPLY:           '211 System status reply',
    DIR_STATUS:                         '212 %s',
    FILE_STATUS:                        '213 %s',
    HELP_MSG:                           '214 help: %s',
    NAME_SYS_TYPE:                      '215 UNIX Type: L8',
    WELCOME_MSG:                        "220 %s",
    SVC_READY_FOR_NEW_USER:             '220 Service ready',
    GOODBYE_MSG:                        '221 Goodbye.',
    DATA_CNX_OPEN_NO_XFR_IN_PROGRESS:   '225 data connection open, no transfer in progress',
    CLOSING_DATA_CNX:                   '226 Abort successful',
    TXFR_COMPLETE_OK:                   '226 Transfer Complete.',
    ENTERING_PASV_MODE:                 '227 Entering Passive Mode (%s).',
    ENTERING_EPSV_MODE:                 '229 Entering Extended Passive Mode (|||%s|).', # where is epsv defined in the rfc's?
    USR_LOGGED_IN_PROCEED:              '230 User logged in, proceed',
    GUEST_LOGGED_IN_PROCEED:            '230 Anonymous login ok, access restrictions apply.',
    REQ_FILE_ACTN_COMPLETED_OK:         '250 Requested File Action Completed OK', #i.e. CWD completed ok
    PWD_REPLY:                          '257 "%s"',
    MKD_REPLY:                          '257 "%s" created',

    # -- 300's --
    'userotp':                          '331 Response to %s.',  # ???
    USR_NAME_OK_NEED_PASS:              '331 Password required for %s.',
    GUEST_NAME_OK_NEED_EMAIL:           '331 Guest login ok, type your email address as password.',

    REQ_FILE_ACTN_PENDING_FURTHER_INFO: '350 Requested file action pending further information.',

# -- 400's --
    SVC_NOT_AVAIL_CLOSING_CTRL_CNX:     '421 Service not available, closing control connection.',
    TOO_MANY_CONNECTIONS:               '421 Too many users right now, try again in a few minutes.',
    CANT_OPEN_DATA_CNX:                 "425 Can't open data connection.",
    CNX_CLOSED_TXFR_ABORTED:            '426 Transfer aborted.  Data connection closed.',

    REQ_ACTN_ABRTD_LOCAL_ERR:           '451 Requested action aborted. Local error in processing.',


    # -- 500's --
    SYNTAX_ERR:                         "500 Syntax error: %s",
    SYNTAX_ERR_IN_ARGS:                 '501 syntax error in argument(s) %s.',
    CMD_NOT_IMPLMNTD:                   "502 Command '%s' not implemented",
    BAD_CMD_SEQ:                        '503 Incorrect sequence of commands: %s',
    CMD_NOT_IMPLMNTD_FOR_PARAM:         "504 Not implemented for parameter '%s'.",
    NOT_LOGGED_IN:                      '530 Please login with USER and PASS.',
    AUTH_FAILURE:                       '530 Sorry, Authentication failed.',
    NEED_ACCT_FOR_STOR:                 '532 Need an account for storing files',
    FILE_NOT_FOUND:                     '550 %s: No such file or directory.',
    PERMISSION_DENIED:                  '550 %s: Permission denied.',
    ANON_USER_DENIED:                   '550 Anonymous users are forbidden to change the filesystem',
    IS_NOT_A_DIR:                       '550 Cannot rmd, %s is not a directory',
    REQ_ACTN_NOT_TAKEN:                 '550 Requested action not taken: %s',
    EXCEEDED_STORAGE_ALLOC:             '552 Requested file action aborted, exceeded file storage allocation',
    FILENAME_NOT_ALLOWED:               '553 Requested action not taken, file name not allowed'
}


class InvalidPath(Exception):
    pass


def toSegments(cwd, path):
    """
    Normalize a path, as represented by a list of strings each
    representing one segment of the path.
    """
    if path.startswith('/'):
        segs = []
    else:
        segs = cwd[:]

    for s in path.split('/'):
        if s == '.' or s == '':
            continue
        elif s == '..':
            if segs:
                segs.pop()
            else:
                raise InvalidPath(cwd, path)
        elif '\0' in s or '/' in s:
            raise InvalidPath(cwd, path)
        else:
            segs.append(s)
    return segs


def errnoToFailure(e, path):
    if e == errno.ENOENT or e == errno.EISDIR:
        return defer.fail(FileNotFoundError(path))
    elif e == errno.EACCES or e == errno.EPERM:
        return defer.fail(PermissionDeniedError(path))
    elif e == errno.ENOTDIR:
        return defer.fail(IsNotADirectoryError(path))
    else:
        return defer.fail()

# Generic VFS exceptions
class FilesystemShellException(Exception):
    """Base for IFTPShell errors.
    """

class NoSuchFile(FilesystemShellException):
    pass

class PermissionDenied(FilesystemShellException):
    pass

class WrongFiletype(FilesystemShellException):
    pass


# -- Custom Exceptions --

class FTPCmdError(Exception):
    def __init__(self, *msg):
        Exception.__init__(self, *msg)
        self.errorMessage = msg

    def response(self):
        """Generate a FTP response message for this error."""
        return RESPONSE[self.errorCode] % self.errorMessage


class FileNotFoundError(FTPCmdError):
    errorCode = FILE_NOT_FOUND


class AnonUserDeniedError(FTPCmdError):
    """
    Raised when an anonymous user issues a command that will alter the
    filesystem
    """
    def __init__(self):
        # No message
        FTPCmdError.__init__(self, None)

    errorCode = ANON_USER_DENIED


class PermissionDeniedError(FTPCmdError):
    """
    Raised when access is attempted to a resource to which access is
    not allowed.
    """
    errorCode = PERMISSION_DENIED


class IsNotADirectoryError(FTPCmdError):
    """raised when RMD is called on a path that isn't a directory
    """
    errorCode = IS_NOT_A_DIR

class CmdSyntaxError(FTPCmdError):
    errorCode = SYNTAX_ERR

class CmdArgSyntaxError(FTPCmdError):
    errorCode = SYNTAX_ERR_IN_ARGS

class CmdNotImplementedError(FTPCmdError):
    """raised when an unimplemented command is given to the server
    """
    errorCode = CMD_NOT_IMPLMNTD

class CmdNotImplementedForArgError(FTPCmdError):
    errorCode = CMD_NOT_IMPLMNTD_FOR_PARAM

class FTPError(Exception):
    pass

class FTPTimeoutError(Exception):
    pass

class DTPError(Exception):
    pass

class BogusClientError(Exception):
    """thrown when a client other than the one we opened this
    DTP connection for attempts to connect, or a client attempts to
    get us to connect to an ip that differs from the one where the
    request came from"""
    pass

class PathBelowTLDError(FTPCmdError):
    errorCode = PERMISSION_DENIED

class ClientDisconnectError(Exception):
    pass

class PortConnectionError(Exception):
    pass

class BadCmdSequenceError(FTPCmdError):
    """raised when a client sends a series of commands in an illogical sequence"""
    errorCode = BAD_CMD_SEQ

class AuthorizationError(FTPCmdError):
    """raised when client authentication fails"""
    errorCode = AUTH_FAILURE


# -- Utility Functions --

def debugDeferred(self, *_):
    log.msg('debugDeferred(): %s' % str(_), debug=True)


# -- DTP Protocol --

class IDTPFactory(components.Interface):
    """An interface for protocol.Factories

    @ivar peerCheck: perform checks to make sure the ftp-pi's peer is the same
        as the dtp's
    @ivar pi: a reference to this factory's protocol interpreter
    """
    def __init__(self, pi, peerHost=None):
        """Constructor
        @param pi: this factory's protocol interpreter
        @param peerHost: if peerCheck is True, this is the tuple that the
            generated instance will use to perform security checks
        """
        pass


_months = [
    None,
    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']

class DTP(object, protocol.Protocol):
    implements(interfaces.IConsumer)

    isConnected = False

    _cons = None
    _onConnLost = None
    _buffer = None

    def connectionMade(self):
        self.isConnected = True
        self.factory.deferred.callback(None)
        self._buffer = []

    def connectionLost(self, reason):
        self.isConnected = False
        if self._onConnLost is not None:
            self._onConnLost.callback(None)

    def sendLine(self, line):
        self.transport.write(line + '\r\n')


    def _formatOneListResponse(self, name, size, directory, permissions, hardlinks, modified, owner, group):
        def formatMode(mode):
            return ''.join([mode & (256 >> n) and 'rwx'[n % 3] or '-' for n in range(9)])

        def formatDate(mtime):
            now = time.gmtime()
            info = {
                'month': _months[mtime.tm_mon],
                'day': mtime.tm_mday,
                'year': mtime.tm_year,
                'hour': mtime.tm_hour,
                'minute': mtime.tm_min
                }
            if now.tm_year != mtime.tm_year:
                return '%(month)s %(day)02d %(year)5d' % info
            else:
                return '%(month)s %(day)02d %(hour)02d:%(minute)02d' % info

        format = ('%(directory)s%(permissions)s%(hardlinks)4d '
                  '%(owner)-9s %(group)-9s %(size)15d %(date)12s '
                  '%(name)s')

        return format % {
            'directory': directory and 'd' or '-',
            'permissions': formatMode(permissions),
            'hardlinks': hardlinks,
            'owner': owner[:8],
            'group': group[:8],
            'size': size,
            'date': formatDate(time.gmtime(modified)),
            'name': name}

    def sendListResponse(self, name, response):
        self.sendLine(self._formatOneListResponse(name, *response))


    # Proxy IConsumer to our transport
    def registerProducer(self, producer, streaming):
        return self.transport.registerProducer(producer, streaming)

    def unregisterProducer(self):
        self.transport.unregisterProducer()
        self.transport.loseConnection()

    def write(self, data):
        if self.isConnected:
            return self.transport.write(data)
        raise Exception("Crap damn crap damn crap damn")


    # Pretend to be a producer, too.
    def _conswrite(self, bytes):
        try:
            self._cons.write(bytes)
        except:
            self._onConnLost.errback()

    def dataReceived(self, bytes):
        if self._cons is not None:
            self._conswrite(bytes)
        else:
            self._buffer.append(bytes)

    def _unregConsumer(self, ignored):
        self._cons.unregisterProducer()
        self._cons = None
        del self._onConnLost
        return ignored

    def registerConsumer(self, cons):
        assert self._cons is None
        self._cons = cons
        self._cons.registerProducer(self, True)
        for chunk in self._buffer:
            self._conswrite(chunk)
        self._buffer = None
        if self.isConnected:
            self._onConnLost = d = defer.Deferred()
            d.addBoth(self._unregConsumer)
            return d
        else:
            self._cons.unregisterProducer()
            self._cons = None
            return defer.succeed(None)

    def resumeProducing(self):
        self.transport.resumeProducing()

    def pauseProducing(self):
        self.transport.pauseProducing()

    def stopProducing(self):
        self.transport.stopProducing()

components.backwardsCompatImplements(DTP)


class DTPFactory(protocol.ClientFactory):
    implements(IDTPFactory)

    # -- configuration variables --
    peerCheck = False

    # -- class variables --
    def __init__(self, pi, peerHost=None):
        self.pi = pi                        # the protocol interpreter that is using this factory
        self.peerHost = peerHost            # the from FTP.transport.peerHost()
        self.deferred = defer.Deferred()    # deferred will fire when instance is connected
        self.deferred.addBoth(lambda ign: (delattr(self, 'deferred'), ign)[1])
        self.delayedCall = None

    def buildProtocol(self, addr):
        log.msg('DTPFactory.buildProtocol', debug=True)
        self.cancelTimeout()
        if self.pi.dtpInstance:   # only create one instance
            return
        p = DTP()
        p.factory = self
        p.pi = self.pi
        self.pi.dtpInstance = p
        return p

    def stopFactory(self):
        log.msg('dtpFactory.stopFactory', debug=True)
        self.cancelTimeout()

    def timeoutFactory(self):
        log.msg('timed out waiting for DTP connection')
        if self.deferred:
            d, self.deferred = self.deferred, None

            # TODO: LEFT OFF HERE!

            d.addErrback(debugDeferred, 'timeoutFactory firing errback')
            d.errback(defer.TimeoutError())
        self.stopFactory()

    def cancelTimeout(self):
        if not self.delayedCall.called and not self.delayedCall.cancelled:
            log.msg('cancelling DTP timeout', debug=True)
            self.delayedCall.cancel()
            assert self.delayedCall.cancelled
            log.msg('timeout has been cancelled', debug=True)

    def setTimeout(self, seconds):
        log.msg('DTPFactory.setTimeout set to %s seconds' % seconds)
        self.delayedCall = reactor.callLater(seconds, self.timeoutFactory)

    def clientConnectionFailed(self, connector, reason):
        self.deferred.errback(PortConnectionError(reason))

components.backwardsCompatImplements(DTPFactory)

# -- FTP-PI (Protocol Interpreter) --

class ASCIIConsumerWrapper(object):
    def __init__(self, cons):
        self.cons = cons
        self.registerProducer = cons.registerProducer
        self.unregisterProducer = cons.unregisterProducer

        assert os.linesep == "\r\n" or len(os.linesep) == 1, "Unsupported platform (yea right like this even exists)"

        if os.linesep == "\r\n":
            self.write = cons.write

    def write(self, bytes):
        return self.cons.write(bytes.replace(os.linesep, "\r\n"))


class FileConsumer(object):
    def __init__(self, fObj):
        self.fObj = fObj

    def registerProducer(self, producer, streaming):
        self.producer = producer
        assert streaming

    def unregisterProducer(self):
        self.producer = None
        self.fObj.close()

    def write(self, bytes):
        self.fObj.write(bytes)


class FTPOverflowProtocol(basic.LineReceiver):
    """FTP mini-protocol for when there are too many connections."""
    def connectionMade(self):
        self.sendLine(RESPONSE[TOO_MANY_CONNECTIONS])
        self.transport.loseConnection()


class FTP(object, basic.LineReceiver, policies.TimeoutMixin):
    """Protocol Interpreter for the File Transfer Protocol

    @ivar state: The current server state.  One of L{UNAUTH},
    L{INAUTH}, L{AUTHED}, L{RENAMING}.

    @ivar shell: The connected avatar
    @ivar binary: The transfer mode.  If false, ASCII.
    @ivar dtpFactory: Generates a single DTP for this session
    @ivar dtpPort: Port returned from listenTCP
    """

    disconnected = False

    # States an FTP can be in
    UNAUTH, INAUTH, AUTHED, RENAMING = range(4)

    # how long the DTP waits for a connection
    dtpTimeout = 10

    portal      = None
    shell       = None     # the avatar
    dtpFactory  = None     # generates a single DTP for this session
    dtpPort     = None     # object returned from listenTCP
    dtpInstance = None
    binary      = True     # binary transfers? False implies ASCII. defaults to True


    def reply(self, key, *args):
        msg = RESPONSE[key] % args
        self.sendLine(msg)


    def connectionMade(self):
        self.state = self.UNAUTH
        self.setTimeout(self.timeOut)
        self.reply(WELCOME_MSG, self.factory.welcomeMessage)

    def connectionLost(self, reason):
        # if we have a DTP protocol instance running and
        # we lose connection to the client's PI, kill the
        # DTP connection and close the port
        if self.dtpFactory:
            self.cleanupDTP()
        self.setTimeout(None)
        if hasattr(self.shell, 'logout') and self.shell.logout is not None:
            self.shell.logout()
        self.shell = None
        self.transport = None

    def timeoutConnection(self):
        self.transport.loseConnection()

    def lineReceived(self, line):
        self.resetTimeout()
        self.pauseProducing()

        def processFailed(err):
            if err.check(FTPCmdError):
                self.sendLine(err.value.response())
            elif (err.check(TypeError) and 
                  err.value.args[0].find('takes exactly') != -1):
                self.reply(SYNTAX_ERR, "%s requires an argument." % (cmd,))
            else:
                log.msg("Unexpected FTP error")
                log.err(err)
                self.reply(REQ_ACTN_NOT_TAKEN, "internal server error")

        def processSucceeded(result):
            if isinstance(result, tuple):
                self.reply(*result)
            elif result is not None:
                self.reply(result)

        def allDone(ignored):
            if not self.disconnected:
                self.resumeProducing()

        spaceIndex = line.find(' ')
        if spaceIndex != -1:
            cmd = line[:spaceIndex]
            args = (line[spaceIndex + 1:],)
        else:
            cmd = line
            args = ()
        d = defer.maybeDeferred(self.processCommand, cmd, *args)
        d.addCallbacks(processSucceeded, processFailed)
        d.addErrback(log.err)

        # XXX It burnsss
        # LineReceiver doesn't let you resumeProducing inside
        # lineReceived atm
        from twisted.internet import reactor
        reactor.callLater(0, d.addBoth, allDone)


    def processCommand(self, cmd, *params):
        cmd = cmd.upper()

        if self.state == self.UNAUTH:
            if cmd == 'USER':
                return self.ftp_USER(*params)
            elif cmd == 'PASS':
                return BAD_CMD_SEQ, "USER required before PASS"
            else:
                return NOT_LOGGED_IN

        elif self.state == self.INAUTH:
            if cmd == 'PASS':
                return self.ftp_PASS(*params)
            else:
                return BAD_CMD_SEQ

        elif self.state == self.AUTHED:
            method = getattr(self, "ftp_" + cmd, None)
            if method is not None:
                return method(*params)
            return defer.fail(CmdNotImplementedError(cmd))

        elif self.state == self.RENAMING:
            if cmd == 'RNTO':
                return self.ftp_RNTO(*params)
            else:
                return BAD_CMD_SEQ, "Blah"


    def ftp_USER(self, username):
        """
        First part of login.  Get the username the peer wants to
        authenticate as.
        """
        if not username:
            return defer.fail(CmdSyntaxError('USER requires an argument'))

        self._user = username
        self.state = self.INAUTH
        if self.factory.allowAnonymous and self._user == self.factory.userAnonymous:
            return GUEST_NAME_OK_NEED_EMAIL
        else:
            return (USR_NAME_OK_NEED_PASS, username)

    # TODO: add max auth try before timeout from ip...
    # TODO: need to implement minimal ABOR command

    def ftp_PASS(self, password):
        """
        Second part of login.  Get the password the peer wants to
        authenticate with.
        """
        if self.factory.allowAnonymous and self._user == self.factory.userAnonymous:
            # anonymous login
            creds = credentials.Anonymous()
            reply = GUEST_LOGGED_IN_PROCEED
        else:
            # user login
            creds = credentials.UsernamePassword(self._user, password)
            reply = USR_LOGGED_IN_PROCEED
        del self._user

        def _cbLogin((interface, avatar, logout)):
            assert interface is IFTPShell, "The realm is busted, jerk."
            self.shell = avatar
            self.logout = logout
            self.workingDirectory = []
            self.state = self.AUTHED
            return reply

        def _ebLogin(failure):
            failure.trap(cred_error.UnauthorizedLogin, cred_error.UnhandledCredentials)
            self.state = self.UNAUTH
            raise AuthorizationError

        d = self.portal.login(creds, None, IFTPShell)
        d.addCallbacks(_cbLogin, _ebLogin)
        return d


    def ftp_PASV(self):
        """Request for a passive connection

        from the rfc:
            This command requests the server-DTP to "listen" on a data
            port (which is not its default data port) and to wait for a
            connection rather than initiate one upon receipt of a
            transfer command.  The response to this command includes the
            host and port address this server is listening on.
        """
        # if we have a DTP port set up, lose it.
        if self.dtpFactory is not None:
            # cleanupDTP sets dtpFactory to none.  Later we'll do
            # cleanup here or something.
            self.cleanupDTP()
        self.dtpFactory = DTPFactory(pi=self)
        self.dtpFactory.setTimeout(self.dtpTimeout)
        self.dtpPort = reactor.listenTCP(0, self.dtpFactory)

        host = self.transport.getHost().host
        port = self.dtpPort.getHost().port
        self.reply(ENTERING_PASV_MODE, encodeHostPort(host, port))
        return self.dtpFactory.deferred.addCallback(lambda ign: None)


    def ftp_PORT(self, address):
        addr = map(int, address.split(','))
        ip = '%d.%d.%d.%d' % tuple(addr[:4])
        port = addr[4] << 8 | addr[5]

        # if we have a DTP port set up, lose it.
        if self.dtpFactory is not None:
            self.cleanupDTP()

        self.dtpFactory = DTPFactory(pi=self, peerHost=self.transport.getPeer().host)
        self.dtpFactory.setTimeout(self.dtpTimeout)
        self.dtpPort = reactor.connectTCP(ip, port, self.dtpFactory)

        def connected(ignored):
            return ENTERING_PORT_MODE
        def connFailed(err):
            err.trap(PortConnectionError)
            return CANT_OPEN_DATA_CNX
        return self.dtpFactory.deferred.addCallbacks(connected, connFailed)


    def ftp_LIST(self, path=''):
        """ This command causes a list to be sent from the server to the
        passive DTP.  If the pathname specifies a directory or other
        group of files, the server should transfer a list of files
        in the specified directory.  If the pathname specifies a
        file then the server should send current information on the
        file.  A null argument implies the user's current working or
        default directory.
        """
        # Uh, for now, do this retarded thing.
        if self.dtpInstance is None or not self.dtpInstance.isConnected:
            return defer.fail(BadCmdSequenceError('must send PORT or PASV before RETR'))

        # bug in konqueror
        if path == "-a":
            path = ''
        # bug in gFTP 2.0.15
        if path == "-aL":
            path = ''
        # bug in Nautilus 2.10.0
        if path == "-L":
            path = ''

        def gotListing(results):
            self.reply(DATA_CNX_ALREADY_OPEN_START_XFR)
            for (name, attrs) in results:
                self.dtpInstance.sendListResponse(name, attrs)
            self.dtpInstance.transport.loseConnection()
            return (TXFR_COMPLETE_OK,)

        try:
            segments = toSegments(self.workingDirectory, path)
        except InvalidPath, e:
            return defer.fail(FileNotFoundError(path))

        d = self.shell.list(
            segments,
            ('size', 'directory', 'permissions', 'hardlinks',
             'modified', 'owner', 'group'))
        d.addCallback(gotListing)
        return d


    def ftp_NLST(self, path):
        # XXX: why is this check different to ftp_RETR/ftp_STOR?
        if self.dtpInstance is None or not self.dtpInstance.isConnected:
            return defer.fail(BadCmdSequenceError('must send PORT or PASV before RETR'))

        try:
            segments = toSegments(self.workingDirectory, path)
        except InvalidPath, e:
            return defer.fail(FileNotFoundError(path))

        def cbList(results):
            self.reply(DATA_CNX_ALREADY_OPEN_START_XFR)
            for (name, ignored) in results:
                self.dtpInstance.sendLine(name)
            self.dtpInstance.transport.loseConnection()
            return (TXFR_COMPLETE_OK,)

        def cbGlob(results):
            self.reply(DATA_CNX_ALREADY_OPEN_START_XFR)
            for (name, ignored) in results:
                if fnmatch.fnmatch(name, segments[-1]):
                    self.dtpInstance.sendLine(name)
            self.dtpInstance.transport.loseConnection()
            return (TXFR_COMPLETE_OK,)

        # XXX Maybe this globbing is incomplete, but who cares.
        # Stupid people probably.
        if segments and (
            '*' in segments[-1] or '?' in segments[-1] or
            ('[' in segments[-1] and ']' in segments[-1])):
            d = self.shell.list(segments[:-1])
            d.addCallback(cbGlob)
        else:
            d = self.shell.list(segments)
            d.addCallback(cbList)
        return d


    def ftp_CWD(self, path):
        try:
            segments = toSegments(self.workingDirectory, path)
        except InvalidPath, e:
            # XXX Eh, what to fail with here?
            return defer.fail(FileNotFoundError(path))

        def accessGranted(result):
            self.workingDirectory = segments
            return (REQ_FILE_ACTN_COMPLETED_OK,)

        return self.shell.access(segments).addCallback(accessGranted)


    def ftp_CDUP(self):
        return self.ftp_CWD('..')


    def ftp_PWD(self):
        return (PWD_REPLY, '/' + '/'.join(self.workingDirectory))


    def ftp_RETR(self, path):
        if self.dtpInstance is None:
            raise BadCmdSequenceError('PORT or PASV required before RETR')

        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))

        # XXX For now, just disable the timeout.  Later we'll want to
        # leave it active and have the DTP connection reset it
        # periodically.
        self.setTimeout(None)

        # Put it back later
        def enableTimeout(result):
            self.setTimeout(self.factory.timeOut)
            return result

        # And away she goes
        if not self.binary:
            cons = ASCIIConsumerWrapper(self.dtpInstance)
        else:
            cons = self.dtpInstance

        def cbSent(result):
            return (TXFR_COMPLETE_OK,)

        def ebSent(err):
            log.msg("Unexpected error attempting to transmit file to client:")
            log.err(err)
            return (CNX_CLOSED_TXFR_ABORTED,)

        def cbOpened(file):
            # Tell them what to doooo
            if self.dtpInstance.isConnected:
                self.reply(DATA_CNX_ALREADY_OPEN_START_XFR)
            else:
                self.reply(FILE_STATUS_OK_OPEN_DATA_CNX)

            d = file.send(cons)
            d.addCallbacks(cbSent, ebSent)
            return d

        def ebOpened(err):
            if not err.check(PermissionDeniedError, FileNotFoundError, IsNotADirectoryError):
                log.msg("Unexpected error attempting to open file for transmission:")
                log.err(err)
            return (FILE_NOT_FOUND, '/'.join(newsegs))

        d = self.shell.openForReading(newsegs)
        d.addCallbacks(cbOpened, ebOpened)
        d.addBoth(enableTimeout)

        # Pass back Deferred that fires when the transfer is done
        return d


    def ftp_STOR(self, path):
        if self.dtpInstance is None:
            raise BadCmdSequenceError('PORT or PASV required before STOR')

        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))

        # XXX For now, just disable the timeout.  Later we'll want to
        # leave it active and have the DTP connection reset it
        # periodically.
        self.setTimeout(None)

        # Put it back later
        def enableTimeout(result):
            self.setTimeout(self.factory.timeOut)
            return result

        def cbSent(result):
            return (TXFR_COMPLETE_OK,)

        def ebSent(err):
            log.msg("Unexpected error receiving file from client:")
            log.err(err)
            return (CNX_CLOSED_TXFR_ABORTED,)

        def cbConsumer(cons):
            if not self.binary:
                cons = ASCIIConsumerWrapper(cons)

            d = self.dtpInstance.registerConsumer(cons)
            d.addCallbacks(cbSent, ebSent)

            # Tell them what to doooo
            if self.dtpInstance.isConnected:
                self.reply(DATA_CNX_ALREADY_OPEN_START_XFR)
            else:
                self.reply(FILE_STATUS_OK_OPEN_DATA_CNX)

            return d

        def cbOpened(file):
            d = file.receive()
            d.addCallback(cbConsumer)
            return d

        def ebOpened(err):
            if not err.check(PermissionDeniedError, FileNotFoundError, IsNotADirectoryError):
                log.msg("Unexpected error attempting to open file for upload:")
                log.err(err)
            return (FILE_NOT_FOUND, '/'.join(newsegs))

        d = self.shell.openForWriting(newsegs)
        d.addCallbacks(cbOpened, ebOpened)
        d.addBoth(enableTimeout)

        # Pass back Deferred that fires when the transfer is done
        return d


    def ftp_SIZE(self, path):
        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))

        def cbStat((size,)):
            return (FILE_STATUS, str(size))

        return self.shell.stat(newsegs, ('size',)).addCallback(cbStat)


    def ftp_MDTM(self, path):
        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))

        def cbStat((modified,)):
            return (FILE_STATUS, time.strftime('%Y%m%d%H%M%S', time.gmtime(modified)))

        return self.shell.stat(newsegs, ('modified',)).addCallback(cbStat)


    def ftp_TYPE(self, type):
        p = type.upper()
        if p:
            f = getattr(self, 'type_' + p[0], None)
            if f is not None:
                return f(p[1:])
            return self.type_UNKNOWN(p)
        return (SYNTAX_ERR,)

    def type_A(self, code):
        if code == '' or code == 'N':
            self.binary = False
            return (TYPE_SET_OK, 'A' + code)
        else:
            return defer.fail(CmdArgSyntaxError(code))

    def type_I(self, code):
        if code == '':
            self.binary = True
            return (TYPE_SET_OK, 'I')
        else:
            return defer.fail(CmdArgSyntaxError(code))

    def type_UNKNOWN(self, code):
        return defer.fail(CmdNotImplementedForArgError(code))



    def ftp_SYST(self):
        return NAME_SYS_TYPE


    def ftp_STRU(self, structure):
        p = structure.upper()
        if p == 'F':
            return (CMD_OK,)
        return defer.fail(CmdNotImplementedForArgError(structure))


    def ftp_MODE(self, mode):
        p = mode.upper()
        if p == 'S':
            return (CMD_OK,)
        return defer.fail(CmdNotImplementedForArgError(mode))


    def ftp_MKD(self, path):
        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))
        return self.shell.makeDirectory(newsegs).addCallback(lambda ign: (MKD_REPLY, path))


    def ftp_RMD(self, path):
        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))
        return self.shell.removeDirectory(newsegs).addCallback(lambda ign: (REQ_FILE_ACTN_COMPLETED_OK,))


    def ftp_DELE(self, path):
        try:
            newsegs = toSegments(self.workingDirectory, path)
        except InvalidPath:
            return defer.fail(FileNotFoundError(path))
        return self.shell.removeFile(newsegs).addCallback(lambda ign: (REQ_FILE_ACTN_COMPLETED_OK,))


    def ftp_NOOP(self):
        return (CMD_OK,)


    def ftp_RNFR(self, fromName):
        self._fromName = fromName
        self.state = self.RENAMING
        return (REQ_FILE_ACTN_PENDING_FURTHER_INFO,)


    def ftp_RNTO(self, toName):
        fromName = self._fromName
        del self._fromName
        self.state = self.AUTHED

        try:
            fromsegs = toSegments(self.workingDirectory, fromName)
            tosegs = toSegments(self.workingDirectory, toName)
        except InvalidPath:
            return defer.fail(FileNotFoundError(fromName))
        return self.shell.rename(fromsegs, tosegs).addCallback(lambda ign: (REQ_FILE_ACTN_COMPLETED_OK,))


    def ftp_QUIT(self):
        self.transport.loseConnection()
        self.disconnected = True


    def cleanupDTP(self):
        """call when DTP connection exits
        """
        log.msg('cleanupDTP', debug=True)

        log.msg(self.dtpPort)
        dtpPort, self.dtpPort = self.dtpPort, None
        if interfaces.IListeningPort.providedBy(dtpPort):
            dtpPort.stopListening()
        elif interfaces.IConnector.providedBy(dtpPort):
            dtpPort.disconnect()
        else:
            assert False, "dtpPort should be an IListeningPort or IConnector, instead is %r" % (dtpPort,)

        self.dtpFactory.stopFactory()
        self.dtpFactory = None

        if self.dtpInstance is not None:
            self.dtpInstance = None

components.backwardsCompatImplements(FTP)


class FTPFactory(policies.LimitTotalConnectionsFactory):
    """A factory for producing ftp protocol instances

    @ivar timeOut: the protocol interpreter's idle timeout time in seconds,
        default is 600 seconds.
    """
    protocol = FTP
    overflowProtocol = FTPOverflowProtocol
    allowAnonymous = True
    userAnonymous = 'anonymous'
    timeOut = 600

    welcomeMessage = "Twisted %s FTP Server" % (copyright.version,)

    def __init__(self, portal=None, userAnonymous='anonymous'):
        self.portal = portal
        self.userAnonymous = 'anonymous'
        self.instances = []

    def buildProtocol(self, addr):
        p = policies.LimitTotalConnectionsFactory.buildProtocol(self, addr)
        if p is not None:
            p.wrappedProtocol.portal = self.portal
            p.wrappedProtocol.timeOut = self.timeOut
        return p

    def stopFactory(self):
        # make sure ftp instance's timeouts are set to None
        # to avoid reactor complaints
        [p.setTimeout(None) for p in self.instances if p.timeOut is not None]
        policies.LimitTotalConnectionsFactory.stopFactory(self)

# -- Cred Objects --

class IFTPShell(Interface):
    """
    An abstraction of the shell commands used by the FTP protocol for
    a given user account.

    All path names must be absolute.
    """

    def makeDirectory(path):
        """
        Create a directory.

        @param path: The path, as a list of segments, to create
        @type path: C{list} of C{unicode}

        @return: A Deferred which fires when the directory has been
        created, or which fails if the directory cannot be created.
        """


    def removeDirectory(path):
        """
        Remove a directory.

        @param path: The path, as a list of segments, to remove
        @type path: C{list} of C{unicode}

        @return: A Deferred which fires when the directory has been
        removed, or which fails if the directory cannot be removed.
        """


    def removeFile(path):
        """
        Remove a file.

        @param path: The path, as a list of segments, to remove
        @type path: C{list} of C{unicode}

        @return: A Deferred which fires when the file has been
        removed, or which fails if the file cannot be removed.
        """


    def rename(fromPath, toPath):
        """
        Rename a file or directory.

        @param fromPath: The current name of the path.
        @type fromPath: C{list} of C{unicode}

        @param toPath: The desired new name of the path.
        @type toPath: C{list} of C{unicode}

        @return: A Deferred which fires when the path has been
        renamed, or which fails if the path cannot be renamed.
        """


    def access(path):
        """
        Determine whether access to the given path is allowed.

        @param path: The path, as a list of segments

        @return: A Deferred which fires with None if access is allowed
        or which fails with a specific exception type if access is
        denied.
        """


    def stat(path, keys=()):
        """
        Retrieve information about the given path.

        This is like list, except it will never return results about
        child paths.
        """


    def list(path, keys=()):
        """
        Retrieve information about the given path.

        If the path represents a non-directory, the result list should
        have only one entry with information about that non-directory.
        Otherwise, the result list should have an element for each
        child of the directory.

        @param path: The path, as a list of segments, to list
        @type path: C{list} of C{unicode}

        @param keys: A tuple of keys desired in the resulting
        dictionaries.

        @return: A Deferred which fires with a list of (name, list),
        where the name is the name of the entry as a unicode string
        and each list contains values corresponding to the requested
        keys.  The following are possible elements of keys, and the
        values which should be returned for them:

            'size': size in bytes, as an integer (this is kinda required)
            'directory': boolean indicating the type of this entry
            'permissions': a bitvector (see os.stat(foo).st_mode)
            'hardlinks': Number of hard links to this entry
            'modified': number of seconds since the epoch since entry was modified
            'owner': string indicating the user owner of this entry
            'group': string indicating the group owner of this entry
        """


    def openForReading(path):
        """
        @param path: The path, as a list of segments, to open
        @type path: C{list} of C{unicode}

        @rtype: C{Deferred} which will fire with L{IReadFile}
        """


    def openForWriting(path):
        """
        @param path: The path, as a list of segments, to open
        @type path: C{list} of C{unicode}

        @rtype: C{Deferred} which will fire with L{IWriteFile}
        """


class IReadFile(Interface):
    """A file out of which bytes may be read.
    """
    def send(consumer):
        """
        Produce the contents of the given path to the given consumer.  This
        method may only be invoked once on each provider.

        @type consumer: C{IConsumer}

        @return: A Deferred which fires when the file has been
        consumed completely.
        """


class IWriteFile(Interface):
    """A file into which bytes may be written.
    """
    def receive():
        """
        Create a consumer which will write to this file.  This method may
        only be invoked once on each provider.

        @rtype: C{Deferred} of C{IConsumer}
        """


def _getgroups(uid):
    """Return the primary and supplementary groups for the given UID.

    @type uid: C{int}
    """
    result = []
    pwent = pwd.getpwuid(uid)

    result.append(pwent.pw_gid)

    for grent in grp.getgrall():
        if pwent.pw_name in grent.gr_mem:
            result.append(grent.gr_gid)

    return result


def _testPermissions(uid, gid, spath, mode='r'):
    """
    checks to see if uid has proper permissions to access path with mode

    @type uid: C{int}
    @param uid: numeric user id

    @type gid: C{int}
    @param gid: numeric group id

    @type spath: C{str}
    @param spath: the path on the server to test

    @type mode: C{str}
    @param mode: 'r' or 'w' (read or write)

    @rtype: C{bool}
    @returns: True if the given credentials have the specified form of
    access to the given path
    """
    if mode == 'r':
        usr = stat.S_IRUSR
        grp = stat.S_IRGRP
        oth = stat.S_IROTH
        amode = os.R_OK
    elif mode == 'w':
        usr = stat.S_IWUSR
        grp = stat.S_IWGRP
        oth = stat.S_IWOTH
        amode = os.W_OK
    else:
        raise ValueError("Invalid mode %r: must specify 'r' or 'w'" % (mode,))

    access = False
    if os.path.exists(spath):
        if uid == 0:
            access = True
        else:
            s = os.stat(spath)
            if usr & s.st_mode and uid == s.st_uid:
                access = True
            elif grp & s.st_mode and gid in _getgroups(uid):
                access = True
            elif oth & s.st_mode:
                access = True

    if access:
        if not os.access(spath, amode):
            access = False
            log.msg("Filesystem grants permission to UID %d but it is inaccessible to me running as UID %d" % (
                uid, os.getuid()))
    return access


class FTPAnonymousShell(object):
    """An anonymous implementation of IFTPShell

    @type filesystemRoot: L{twisted.python.filepath.FilePath}
    @ivar filesystemRoot: The path which is considered the root of
    this shell.
    """
    implements(IFTPShell)

    def __init__(self, filesystemRoot):
        self.filesystemRoot = filesystemRoot

    def _path(self, path):
        return reduce(filepath.FilePath.child, path, self.filesystemRoot)

    def makeDirectory(self, path):
        return defer.fail(AnonUserDeniedError())

    def removeDirectory(self, path):
        return defer.fail(AnonUserDeniedError())

    def removeFile(self, path):
        return defer.fail(AnonUserDeniedError())

    def rename(self, fromPath, toPath):
        return defer.fail(AnonUserDeniedError())

    def receive(self, path):
        path = self._path(path)
        return defer.fail(AnonUserDeniedError())

    def openForReading(self, path):
        p = self._path(path)
        try:
            f = p.open('rb')
        except (IOError, OSError), e:
            return errnoToFailure(e.errno, path)
        except:
            return defer.fail()
        else:
            return defer.succeed(_FileReader(f))

    def access(self, path):
        p = self._path(path)
        # For now, just see if we can os.listdir() it
        try:
            p.listdir()
        except (IOError, OSError), e:
            return errnoToFailure(e.errno, path)
        except:
            return defer.fail()
        else:
            return defer.succeed(None)

    def stat(self, path, keys=()):
        p = self._path(path)
        if p.isdir():
            return defer.fail(WrongFiletype())
        else:
            return self.list(path, keys).addCallback(lambda res: res[0][1])

    def list(self, path, keys=()):
        path = self._path(path)
        if path.isdir():
            entries = path.listdir()
        else:
            entries = [None]

        results = []
        for fName in entries:
            ent = []
            results.append((fName, ent))
            if keys:
                if fName is not None:
                    p = os.path.join(path.path, fName)
                else:
                    p = path.path
                try:
                    statResult = os.stat(p)
                except (IOError, OSError), e:
                    return errnoToFailure(e.errno, path)
                except:
                    return defer.fail()

            for k in keys:
                ent.append(getattr(self, '_list_' + k)(statResult))
        return defer.succeed(results)

    _list_size = operator.attrgetter('st_size')
    _list_permissions = operator.attrgetter('st_mode')
    _list_hardlinks = operator.attrgetter('st_nlink')
    _list_modified = operator.attrgetter('st_mtime')

    def _list_owner(self, st):
        if pwd is not None:
            try:
                return pwd.getpwuid(st.st_uid)[0]
            except KeyError:
                pass
        return str(st.st_uid)

    def _list_group(self, st):
        if grp is not None:
            try:
                return grp.getgrgid(st.st_gid)[0]
            except KeyError:
                pass
        return str(st.st_gid)

    def _list_directory(self, st):
        return bool(st.st_mode & stat.S_IFDIR)


class _FileReader(object):
    implements(IReadFile)

    def __init__(self, fObj):
        self.fObj = fObj
        self._send = False

    def _close(self, passthrough):
        self._send = True
        self.fObj.close()
        return passthrough

    def send(self, consumer):
        assert not self._send, "Can only call IReadFile.send *once* per instance"
        self._send = True
        d = basic.FileSender().beginFileTransfer(self.fObj, consumer)
        d.addBoth(self._close)
        return d


class FTPShell(FTPAnonymousShell):
    def makeDirectory(self, path):
        p = self._path(path)
        try:
            p.makedirs()
        except:
            return defer.fail()
        else:
            return defer.succeed(None)


    def removeDirectory(self, path):
        p = self._path(path)
        try:
            os.rmdir(p.path)
        except:
            return defer.fail()
        else:
            return defer.succeed(None)


    def removeFile(self, path):
        p = self._path(path)
        try:
            p.remove()
        except:
            return defer.fail()
        else:
            return defer.succeed(None)


    def rename(self, fromPath, toPath):
        fp = self._path(fromPath)
        tp = self._path(toPath)
        try:
            os.rename(fp.path, tp.path)
        except:
            return defer.fail()
        else:
            return defer.succeed(None)


    def openForWriting(self, path):
        p = self._path(path)
        try:
            fObj = p.open('wb')
        except:
            return defer.fail()
        return defer.succeed(_FileWriter(fObj))


class _FileWriter(object):
    implements(IWriteFile)

    def __init__(self, fObj):
        self.fObj = fObj
        self._receive = False

    def receive(self):
        assert not self._receive, "Can only call IWriteFile.receive *once* per instance"
        self._receive = True
        # FileConsumer will close the file object
        return defer.succeed(FileConsumer(self.fObj))


class FTPRealm:
    """
    @type anonymousRoot: L{twisted.python.filepath.FilePath}
    @ivar anonymousRoot: Root of the filesystem to which anonymous
    users will be granted access.
    """
    implements(portal.IRealm)

    def __init__(self, anonymousRoot):
        self.anonymousRoot = filepath.FilePath(anonymousRoot)

    def requestAvatar(self, avatarId, mind, *interfaces):
        for iface in interfaces:
            if iface is IFTPShell:
                if avatarId is checkers.ANONYMOUS:
                    avatar = FTPAnonymousShell(self.anonymousRoot)
                else:
                    avatar = FTPShell(filepath.FilePath("/home/" + avatarId))
                return IFTPShell, avatar, getattr(avatar, 'logout', lambda: None)
        raise NotImplementedError("Only IFTPShell interface is supported by this realm")
components.backwardsCompatImplements(FTPRealm)


# --- FTP CLIENT  -------------------------------------------------------------

####
# And now for the client...

# Notes:
#   * Reference: http://cr.yp.to/ftp.html
#   * FIXME: Does not support pipelining (which is not supported by all
#     servers anyway).  This isn't a functionality limitation, just a
#     small performance issue.
#   * Only has a rudimentary understanding of FTP response codes (although
#     the full response is passed to the caller if they so choose).
#   * Assumes that USER and PASS should always be sent
#   * Always sets TYPE I  (binary mode)
#   * Doesn't understand any of the weird, obscure TELNET stuff (\377...)
#   * FIXME: Doesn't share any code with the FTPServer

class ConnectionLost(FTPError):
    pass

class CommandFailed(FTPError):
    pass

class BadResponse(FTPError):
    pass

class UnexpectedResponse(FTPError):
    pass

class UnexpectedData(FTPError):
    pass

class FTPCommand:
    def __init__(self, text=None, public=0):
        self.text = text
        self.deferred = defer.Deferred()
        self.ready = 1
        self.public = public
        self.transferDeferred = None

    def fail(self, failure):
        if self.public:
            self.deferred.errback(failure)


class ProtocolWrapper(protocol.Protocol):
    def __init__(self, original, deferred):
        self.original = original
        self.deferred = deferred
    def makeConnection(self, transport):
        self.original.makeConnection(transport)
    def dataReceived(self, data):
        self.original.dataReceived(data)
    def connectionLost(self, reason):
        self.original.connectionLost(reason)
        # Signal that transfer has completed
        self.deferred.callback(None)


class SenderProtocol(protocol.Protocol):
    implements(interfaces.IFinishableConsumer)

    def __init__(self):
        # Fired upon connection
        self.connectedDeferred = defer.Deferred()

        # Fired upon disconnection
        self.deferred = defer.Deferred()

    #Protocol stuff
    def dataReceived(self, data):
        raise UnexpectedData(
            "Received data from the server on a "
            "send-only data-connection"
        )

    def makeConnection(self, transport):
        protocol.Protocol.makeConnection(self, transport)
        self.connectedDeferred.callback(self)

    def connectionLost(self, reason):
        if reason.check(error.ConnectionDone):
            self.deferred.callback('connection done')
        else:
            self.deferred.errback(reason)

    #IFinishableConsumer stuff
    def write(self, data):
        self.transport.write(data)

    def registerProducer(self):
        pass

    def unregisterProducer(self):
        pass

    def finish(self):
        self.transport.loseConnection()

components.backwardsCompatImplements(SenderProtocol)


def decodeHostPort(line):
    """Decode an FTP response specifying a host and port.

    @returns: a 2-tuple of (host, port).
    """
    abcdef = re.sub('[^0-9, ]', '', line)
    a, b, c, d, e, f = map(str.strip, abcdef.split(','))
    host = "%s.%s.%s.%s" % (a, b, c, d)
    port = (int(e)<<8) + int(f)
    return host, port

def encodeHostPort(host, port):
    numbers = host.split('.') + [str(port >> 8), str(port % 256)]
    return ','.join(numbers)

def _unwrapFirstError(failure):
    failure.trap(defer.FirstError)
    return failure.value.subFailure

class FTPDataPortFactory(protocol.ServerFactory):
    """Factory for data connections that use the PORT command

    (i.e. "active" transfers)
    """
    noisy = 0
    def buildProtocol(self, addr):
        # This is a bit hackish -- we already have a Protocol instance,
        # so just return it instead of making a new one
        # FIXME: Reject connections from the wrong address/port
        #        (potential security problem)
        self.protocol.factory = self
        self.port.loseConnection()
        return self.protocol


class FTPClientBasic(basic.LineReceiver):
    """Foundations of an FTP client."""
    debug = 0
    def __init__(self):
        self.actionQueue = []
        self.greeting = None
        self.nextDeferred = defer.Deferred().addCallback(self._cb_greeting)
        self.nextDeferred.addErrback(self.fail)
        self.response = []
        self._failed = 0

    def fail(self, error):
        """Give an error to any queued deferreds."""
        self._fail(error)

    def _fail(self, error):
        """Errback all queued deferreds."""
        if self._failed:
            # We're recursing; bail out here for simplicity
            return error
        self._failed = 1
        for ftpCommand in self.actionQueue:
            ftpCommand.fail(failure.Failure(ConnectionLost('FTP connection lost', error)))
        return error

    def _cb_greeting(self, greeting):
        self.greeting = greeting

    def sendLine(self, line):
        """(Private) Sends a line, unless line is None."""
        if line is None:
            return
        basic.LineReceiver.sendLine(self, line)

    def sendNextCommand(self):
        """(Private) Processes the next command in the queue."""
        ftpCommand = self.popCommandQueue()
        if ftpCommand is None:
            self.nextDeferred = None
            return
        if not ftpCommand.ready:
            self.actionQueue.insert(0, ftpCommand)
            reactor.callLater(1.0, self.sendNextCommand)
            self.nextDeferred = None
            return

        # FIXME: this if block doesn't belong in FTPClientBasic, it belongs in
        #        FTPClient.
        if ftpCommand.text == 'PORT':
            self.generatePortCommand(ftpCommand)

        if self.debug:
            log.msg('<-- %s' % ftpCommand.text)
        self.nextDeferred = ftpCommand.deferred
        self.sendLine(ftpCommand.text)

    def queueCommand(self, ftpCommand):
        """Add an FTPCommand object to the queue.

        If it's the only thing in the queue, and we are connected and we aren't
        waiting for a response of an earlier command, the command will be sent
        immediately.

        @param ftpCommand: an L{FTPCommand}
        """
        self.actionQueue.append(ftpCommand)
        if (len(self.actionQueue) == 1 and self.transport is not None and
            self.nextDeferred is None):
            self.sendNextCommand()

    def queueStringCommand(self, command, public=1):
        """Queues a string to be issued as an FTP command

        @param command: string of an FTP command to queue
        @param public: a flag intended for internal use by FTPClient.  Don't
            change it unless you know what you're doing.

        @returns: a L{Deferred} that will be called when the response to the
        command has been received.
        """
        ftpCommand = FTPCommand(command, public)
        self.queueCommand(ftpCommand)
        return ftpCommand.deferred

    def popCommandQueue(self):
        """Return the front element of the command queue, or None if empty."""
        if self.actionQueue:
            return self.actionQueue.pop(0)
        else:
            return None

    def queueLogin(self, username, password):
        """Login: send the username, send the password.
        
        If the password is C{None}, the PASS command won't be sent.  Also, if
        the response to the USER command has a response code of 230 (User logged
        in), then PASS won't be sent either.
        """
        # Prepare the USER command
        deferreds = []
        userDeferred = self.queueStringCommand('USER ' + username, public=0)
        deferreds.append(userDeferred)
        
        # Prepare the PASS command (if a password is given)
        if password is not None:
            passwordCmd = FTPCommand('PASS ' + password, public=0)
            self.queueCommand(passwordCmd)
            deferreds.append(passwordCmd.deferred)

            # Avoid sending PASS if the response to USER is 230.
            # (ref: http://cr.yp.to/ftp/user.html#user)
            def cancelPasswordIfNotNeeded(response):
                if response[0].startswith('230'):
                    # No password needed!
                    self.actionQueue.remove(passwordCmd)
                return response
            userDeferred.addCallback(cancelPasswordIfNotNeeded)

        # Error handling.
        for deferred in deferreds:
            # If something goes wrong, call fail
            deferred.addErrback(self.fail)
            # But also swallow the error, so we don't cause spurious errors
            deferred.addErrback(lambda x: None)

    def lineReceived(self, line):
        """(Private) Parses the response messages from the FTP server."""
        # Add this line to the current response
        if self.debug:
            log.msg('--> %s' % line)
        self.response.append(line)

        # Bail out if this isn't the last line of a response
        # The last line of response starts with 3 digits followed by a space
        codeIsValid = re.match(r'\d{3} ', line)
        if not codeIsValid:
            return
        
        code = line[0:3]

        # Ignore marks
        if code[0] == '1':
            return

        # Check that we were expecting a response
        if self.nextDeferred is None:
            self.fail(UnexpectedResponse(self.response))
            return

        # Reset the response
        response = self.response
        self.response = []

        # Look for a success or error code, and call the appropriate callback
        if code[0] in ('2', '3'):
            # Success
            self.nextDeferred.callback(response)
        elif code[0] in ('4', '5'):
            # Failure
            self.nextDeferred.errback(failure.Failure(CommandFailed(response)))
        else:
            # This shouldn't happen unless something screwed up.
            log.msg('Server sent invalid response code %s' % (code,))
            self.nextDeferred.errback(failure.Failure(BadResponse(response)))

        # Run the next command
        self.sendNextCommand()

    def connectionLost(self, reason):
        self._fail(reason)



class _PassiveConnectionFactory(protocol.ClientFactory):
    noisy = False

    def __init__(self, protoInstance):
        self.protoInstance = protoInstance

    def buildProtocol(self, ignored):
        self.protoInstance.factory = self
        return self.protoInstance

    def clientConnectionFailed(self, connector, reason):
        e = FTPError('Connection Failed', reason)
        self.protoInstance.deferred.errback(e)



class FTPClient(FTPClientBasic):
    """A Twisted FTP Client

    Supports active and passive transfers.

    This class is semi-stable.

    @ivar passive: See description in __init__.
    """
    def __init__(self, username='anonymous',
                 password='twisted@twistedmatrix.com',
                 passive=1):
        """Constructor.

        I will login as soon as I receive the welcome message from the server.

        @param username: FTP username
        @param password: FTP password
        @param passive: flag that controls if I use active or passive data
            connections.  You can also change this after construction by
            assigning to self.passive.
        """
        FTPClientBasic.__init__(self)
        self.queueLogin(username, password)

        self.passive = passive

    def fail(self, error):
        """Disconnect, and also give an error to any queued deferreds."""
        self.transport.loseConnection()
        self._fail(error)

    def receiveFromConnection(self, commands, protocol):
        """
        Retrieves a file or listing generated by the given command,
        feeding it to the given protocol.

        @param command: list of strings of FTP commands to execute then receive
            the results of (e.g. LIST, RETR)
        @param protocol: A L{Protocol} *instance* e.g. an
            L{FTPFileListProtocol}, or something that can be adapted to one.
            Typically this will be an L{IConsumer} implemenation.

        @returns: L{Deferred}.
        """
        protocol = interfaces.IProtocol(protocol)
        wrapper = ProtocolWrapper(protocol, defer.Deferred())
        return self._openDataConnection(commands, wrapper)

    def queueLogin(self, username, password):
        """Login: send the username, send the password, and
        set retrieval mode to binary
        """
        FTPClientBasic.queueLogin(self, username, password)
        d = self.queueStringCommand('TYPE I', public=0)
        # If something goes wrong, call fail
        d.addErrback(self.fail)
        # But also swallow the error, so we don't cause spurious errors
        d.addErrback(lambda x: None)

    def sendToConnection(self, commands):
        """XXX

        @returns: A tuple of two L{Deferred}s:
                  - L{Deferred} L{IFinishableConsumer}. You must call
                    the C{finish} method on the IFinishableConsumer when the file
                    is completely transferred.
                  - L{Deferred} list of control-connection responses.
        """
        s = SenderProtocol()
        r = self._openDataConnection(commands, s)
        return (s.connectedDeferred, r)

    def _openDataConnection(self, commands, protocol):
        """
        This method returns a DeferredList.
        """
        cmds = [FTPCommand(command, public=1) for command in commands]
        cmdsDeferred = defer.DeferredList([cmd.deferred for cmd in cmds],
                                    fireOnOneErrback=True, consumeErrors=True)
        cmdsDeferred.addErrback(_unwrapFirstError)

        if self.passive:
            # Hack: use a mutable object to sneak a variable out of the
            # scope of doPassive
            _mutable = [None]
            def doPassive(response):
                """Connect to the port specified in the response to PASV"""
                host, port = decodeHostPort(response[-1][4:])

                f = _PassiveConnectionFactory(protocol)
                _mutable[0] = reactor.connectTCP(host, port, f)

            pasvCmd = FTPCommand('PASV')
            self.queueCommand(pasvCmd)
            pasvCmd.deferred.addCallback(doPassive).addErrback(self.fail)

            results = [cmdsDeferred, pasvCmd.deferred, protocol.deferred]
            d = defer.DeferredList(results, fireOnOneErrback=1, consumeErrors=1)
            d.addErrback(_unwrapFirstError)

            # Ensure the connection is always closed
            def close(x, m=_mutable):
                m[0] and m[0].disconnect()
                return x
            d.addBoth(close)

        else:
            # We just place a marker command in the queue, and will fill in
            # the host and port numbers later (see generatePortCommand)
            portCmd = FTPCommand('PORT')

            # Ok, now we jump through a few hoops here.
            # This is the problem: a transfer is not to be trusted as complete
            # until we get both the "226 Transfer complete" message on the
            # control connection, and the data socket is closed.  Thus, we use
            # a DeferredList to make sure we only fire the callback at the
            # right time.

            portCmd.transferDeferred = protocol.deferred
            portCmd.protocol = protocol
            portCmd.deferred.addErrback(portCmd.transferDeferred.errback)
            self.queueCommand(portCmd)

            # Create dummy functions for the next callback to call.
            # These will also be replaced with real functions in
            # generatePortCommand.
            portCmd.loseConnection = lambda result: result
            portCmd.fail = lambda error: error

            # Ensure that the connection always gets closed
            cmdsDeferred.addErrback(lambda e, pc=portCmd: pc.fail(e) or e)

            results = [cmdsDeferred, portCmd.deferred, portCmd.transferDeferred]
            d = defer.DeferredList(results, fireOnOneErrback=1, consumeErrors=1)
            # XXX: d.addErrback(_unwrapFirstError), but add a test.

        for cmd in cmds:
            self.queueCommand(cmd)
        return d

    def generatePortCommand(self, portCmd):
        """(Private) Generates the text of a given PORT command"""

        # The problem is that we don't create the listening port until we need
        # it for various reasons, and so we have to muck about to figure out
        # what interface and port it's listening on, and then finally we can
        # create the text of the PORT command to send to the FTP server.

        # FIXME: This method is far too ugly.

        # FIXME: The best solution is probably to only create the data port
        #        once per FTPClient, and just recycle it for each new download.
        #        This should be ok, because we don't pipeline commands.

        # Start listening on a port
        factory = FTPDataPortFactory()
        factory.protocol = portCmd.protocol
        listener = reactor.listenTCP(0, factory)
        factory.port = listener

        # Ensure we close the listening port if something goes wrong
        def listenerFail(error, listener=listener):
            if listener.connected:
                listener.loseConnection()
            return error
        portCmd.fail = listenerFail

        # Construct crufty FTP magic numbers that represent host & port
        host = self.transport.getHost().host
        port = listener.getHost().port
        portCmd.text = 'PORT ' + encodeHostPort(host, port)

    def escapePath(self, path):
        """Returns a FTP escaped path (replace newlines with nulls)"""
        # Escape newline characters
        return path.replace('\n', '\0')

    def retrieveFile(self, path, protocol, offset=0):
        """Retrieve a file from the given path

        This method issues the 'RETR' FTP command.

        The file is fed into the given Protocol instance.  The data connection
        will be passive if self.passive is set.

        @param path: path to file that you wish to receive.
        @param protocol: a L{Protocol} instance.
        @param offset: offset to start downloading from

        @returns: L{Deferred}
        """
        cmds = ['RETR ' + self.escapePath(path)]
        if offset:
            cmds.insert(0, ('REST ' + str(offset)))
        return self.receiveFromConnection(cmds, protocol)

    retr = retrieveFile

    def storeFile(self, path, offset=0):
        """Store a file at the given path.

        This method issues the 'STOR' FTP command.

        @returns: A tuple of two L{Deferred}s:
                  - L{Deferred} L{IFinishableConsumer}. You must call
                    the C{finish} method on the IFinishableConsumer when the file
                    is completely transferred.
                  - L{Deferred} list of control-connection responses.
        """
        cmds = ['STOR ' + self.escapePath(path)]
        if offset:
            cmds.insert(0, ('REST ' + str(offset)))
        return self.sendToConnection(cmds)

    stor = storeFile

    def list(self, path, protocol):
        """Retrieve a file listing into the given protocol instance.

        This method issues the 'LIST' FTP command.

        @param path: path to get a file listing for.
        @param protocol: a L{Protocol} instance, probably a
            L{FTPFileListProtocol} instance.  It can cope with most common file
            listing formats.

        @returns: L{Deferred}
        """
        if path is None:
            path = ''
        return self.receiveFromConnection(['LIST ' + self.escapePath(path)], protocol)

    def nlst(self, path, protocol):
        """Retrieve a short file listing into the given protocol instance.

        This method issues the 'NLST' FTP command.

        NLST (should) return a list of filenames, one per line.

        @param path: path to get short file listing for.
        @param protocol: a L{Protocol} instance.
        """
        if path is None:
            path = ''
        return self.receiveFromConnection(['NLST ' + self.escapePath(path)], protocol)

    def cwd(self, path):
        """Issues the CWD (Change Working Directory) command.

        @returns: a L{Deferred} that will be called when done.
        """
        return self.queueStringCommand('CWD ' + self.escapePath(path))

    def cdup(self):
        """Issues the CDUP (Change Directory UP) command.

        @returns: a L{Deferred} that will be called when done.
        """
        return self.queueStringCommand('CDUP')

    def pwd(self):
        """Issues the PWD (Print Working Directory) command.

        @returns: a L{Deferred} that will be called when done.  It is up to the
            caller to interpret the response, but the L{parsePWDResponse} method
            in this module should work.
        """
        return self.queueStringCommand('PWD')

    def quit(self):
        """Issues the QUIT command."""
        return self.queueStringCommand('QUIT')


class FTPFileListProtocol(basic.LineReceiver):
    """Parser for standard FTP file listings

    This is the evil required to match::

        -rw-r--r--   1 root     other        531 Jan 29 03:26 README

    If you need different evil for a wacky FTP server, you can
    override either C{fileLinePattern} or C{parseDirectoryLine()}.

    It populates the instance attribute self.files, which is a list containing
    dicts with the following keys (examples from the above line):
        - filetype:   e.g. 'd' for directories, or '-' for an ordinary file
        - perms:      e.g. 'rw-r--r--'
        - nlinks:     e.g. 1
        - owner:      e.g. 'root'
        - group:      e.g. 'other'
        - size:       e.g. 531
        - date:       e.g. 'Jan 29 03:26'
        - filename:   e.g. 'README'
        - linktarget: e.g. 'some/file'

    Note that the 'date' value will be formatted differently depending on the
    date.  Check U{http://cr.yp.to/ftp.html} if you really want to try to parse
    it.

    @ivar files: list of dicts describing the files in this listing
    """
    fileLinePattern = re.compile(
        r'^(?P<filetype>.)(?P<perms>.{9})\s+(?P<nlinks>\d*)\s*'
        r'(?P<owner>\S+)\s+(?P<group>\S+)\s+(?P<size>\d+)\s+'
        r'(?P<date>...\s+\d+\s+[\d:]+)\s+(?P<filename>([^ ]|\\ )*?)'
        r'( -> (?P<linktarget>[^\r]*))?\r?$'
    )
    delimiter = '\n'

    def __init__(self):
        self.files = []

    def lineReceived(self, line):
        d = self.parseDirectoryLine(line)
        if d is None:
            self.unknownLine(line)
        else:
            self.addFile(d)

    def parseDirectoryLine(self, line):
        """Return a dictionary of fields, or None if line cannot be parsed.

        @param line: line of text expected to contain a directory entry
        @type line: str

        @return: dict
        """
        match = self.fileLinePattern.match(line)
        if match is None:
            return None
        else:
            d = match.groupdict()
            d['filename'] = d['filename'].replace(r'\ ', ' ')
            d['nlinks'] = int(d['nlinks'])
            d['size'] = int(d['size'])
            if d['linktarget']:
                d['linktarget'] = d['linktarget'].replace(r'\ ', ' ')
            return d

    def addFile(self, info):
        """Append file information dictionary to the list of known files.

        Subclasses can override or extend this method to handle file
        information differently without affecting the parsing of data
        from the server.

        @param info: dictionary containing the parsed representation
                     of the file information
        @type info: dict
        """
        self.files.append(info)

    def unknownLine(self, line):
        """Deal with received lines which could not be parsed as file
        information.

        Subclasses can override this to perform any special processing
        needed.

        @param line: unparsable line as received
        @type line: str
        """
        pass

def parsePWDResponse(response):
    """Returns the path from a response to a PWD command.

    Responses typically look like::

        257 "/home/andrew" is current directory.

    For this example, I will return C{'/home/andrew'}.

    If I can't find the path, I return C{None}.
    """
    match = re.search('".*"', response)
    if match:
        return match.groups()[0]
    else:
        return None
