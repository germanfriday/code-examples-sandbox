##############################################################################
#
# Copyright (c) 2004 Zope Corporation and Contributors.
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
"""Utility Vocabulary.

This vocabulary provides terms for all utilities providing a given interface.

$Id: vocabulary.py 39752 2005-10-30 20:16:09Z srichter $
"""
__docformat__ = "reStructuredText"

from zope.interface import implements, Interface
from zope.interface.interfaces import IInterface
from zope.interface.verify import verifyObject
from zope.schema.interfaces import IVocabulary, IVocabularyTokenized
from zope.schema.interfaces import ITokenizedTerm

from zope.app import zapi
from zope.app.i18n import ZopeMessageFactory as _
from zope.app.interface.vocabulary import ObjectInterfacesVocabulary

from interfaces import IUtilityRegistration


class UtilityTerm(object):
    """A term representing a utility.

    The token of the term is the name of the utility. Here is a brief example
    on how the IVocabulary interface is handled in this term as a utility:

    >>> term = UtilityTerm(IVocabulary, 'zope.schema.interfaces.IVocabulary')
    >>> verifyObject(ITokenizedTerm, term)
    True

    >>> term.value
    <InterfaceClass zope.schema.interfaces.IVocabulary>
    >>> term.token
    'zope.schema.interfaces.IVocabulary'

    >>> term
    <UtiltiyTerm zope.schema.interfaces.IVocabulary, instance of InterfaceClass>
    """
    implements(ITokenizedTerm)

    def __init__(self, value, token):
        """Create a term for value and token."""
        self.value = value
        self.token = token

    def __repr__(self):
        return '<UtiltiyTerm %s, instance of %s>' %(
            self.token, self.value.__class__.__name__)


class UtilityVocabulary(object):
    """Vocabulary that provides utilities of a specified interface.

    Here is a short example of how the vocabulary should work.

    First we need to create a utility interface and some utilities:

    >>> class IObject(Interface):
    ...     'Simple interface to mark object utilities.'
    >>>
    >>> class Object(object):
    ...     implements(IObject)
    ...     def __init__(self, name):
    ...         self.name = name
    ...     def __repr__(self):
    ...         return '<Object %s>' %self.name

    Now we register some utilities for IObject

    >>> from zope.app.testing import ztapi
    >>> object1 = Object('object1')
    >>> ztapi.provideUtility(IObject, object1, 'object1')
    >>> object2 = Object('object2')
    >>> ztapi.provideUtility(IObject, object2, 'object2')
    >>> object3 = Object('object3')
    >>> ztapi.provideUtility(IObject, object3, 'object3')
    >>> object4 = Object('object4')

    We are now ready to create a vocabulary that we can use; in our case
    everything is global, so the context is None.

    >>> vocab = UtilityVocabulary(None, IObject)
    >>> import pprint
    >>> pprint.pprint(vocab._terms.items())
    [(u'object1', <UtiltiyTerm object1, instance of Object>),
     (u'object2', <UtiltiyTerm object2, instance of Object>),
     (u'object3', <UtiltiyTerm object3, instance of Object>)]

    Now let's see how the other methods behave in this context. First we can
    just use the 'in' opreator to test whether a value is available.

    >>> object1 in vocab
    True
    >>> object4 in vocab
    False

    We can also create a lazy iterator. Note that the utility terms might
    appear in a different order than the utilities were registered.

    >>> iterator = iter(vocab)
    >>> terms = list(iterator)
    >>> names = [term.token for term in terms]
    >>> names.sort()
    >>> names
    [u'object1', u'object2', u'object3']

    Determining the amount of utilities available via the vocabulary is also
    possible.

    >>> len(vocab)
    3

    Next we are looking at some of the more vocabulary-characteristic API
    methods.

    One can get a term for a given value using ``getTerm()``:

    >>> vocab.getTerm(object1)
    <UtiltiyTerm object1, instance of Object>
    >>> vocab.getTerm(object4)
    Traceback (most recent call last):
    ...
    LookupError: <Object object4>

    On the other hand, if you want to get a term by the token, then you do
    that with:

    >>> vocab.getTermByToken('object1')
    <UtiltiyTerm object1, instance of Object>
    >>> vocab.getTermByToken('object4')
    Traceback (most recent call last):
    ...
    LookupError: object4

    That's it. It is all pretty straight forward, but it allows us to easily
    create a vocabulary for any utility. In fact, to make it easy to register
    such a vocabulary via ZCML, the `interface` argument to the constructor
    can be a string that is resolved via the utility registry. The ZCML looks
    like this:

    <zope:vocabulary
        name='IObjects'
        factory='zope.app.utility.vocabulary.UtilityVocabulary'
        interface='zope.app.utility.vocabulary.IObject' />

    >>> ztapi.provideUtility(IInterface, IObject,
    ...                      'zope.app.utility.vocabulary.IObject')
    >>> vocab = UtilityVocabulary(None, 'zope.app.utility.vocabulary.IObject')
    >>> pprint.pprint(vocab._terms.items())
    [(u'object1', <UtiltiyTerm object1, instance of Object>),
     (u'object2', <UtiltiyTerm object2, instance of Object>),
     (u'object3', <UtiltiyTerm object3, instance of Object>)]

    Sometimes it is desirable to only select the name of a utility. For
    this purpose a `nameOnly` argument was added to the constructor, in which
    case the UtilityTerm's value is not the utility itself but the name of the
    utility.

    >>> vocab = UtilityVocabulary(None, IObject, nameOnly=True)
    >>> pprint.pprint([term.value for term in vocab])
    [u'object1', u'object2', u'object3']
    """

    implements(IVocabularyTokenized)

    def __init__(self, context, interface, nameOnly=False):
        if nameOnly is not False:
            nameOnly = True
        if isinstance(interface, (str, unicode)):
            interface = zapi.getUtility(IInterface, interface)
        self.interface = interface
        utils = zapi.getUtilitiesFor(interface, context)
        self._terms = dict([(name, UtilityTerm(nameOnly and name or util, name))
                            for name, util in utils])

    def __contains__(self, value):
        """See zope.schema.interfaces.IBaseVocabulary"""
        return value in [term.value for term in self._terms.values()]

    def getTerm(self, value):
        """See zope.schema.interfaces.IBaseVocabulary"""
        try:
            return [term for name, term in self._terms.items()
                    if term.value == value][0]
        except IndexError:
            raise LookupError(value)

    def getTermByToken(self, token):
        """See zope.schema.interfaces.IVocabularyTokenized"""
        try:
            return self._terms[token]
        except KeyError:
            raise LookupError(token)

    def __iter__(self):
        """See zope.schema.interfaces.IIterableVocabulary"""
        # Sort the terms by the token (utility name)
        values = self._terms.values()
        values.sort(lambda x, y: cmp(x.token, y.token))
        return iter(values)

    def __len__(self):
        """See zope.schema.interfaces.IIterableVocabulary"""
        return len(self._terms)


class UtilityComponentInterfacesVocabulary(ObjectInterfacesVocabulary):

    def __init__(self, context):
        if IUtilityRegistration.providedBy(context):
            context = context.component
        super(UtilityComponentInterfacesVocabulary, self).__init__(
            context)


class UtilityNameTerm:
    r"""Simple term that provides a utility name as a value.

    >>> t1 = UtilityNameTerm('abc')
    >>> t2 = UtilityNameTerm(u'\xC0\xDF\xC7')
    >>> t1.value
    u'abc'
    >>> t2.value
    u'\xc0\xdf\xc7'
    >>> t1.title()
    u'abc'
    >>> repr(t2.title())
    "u'\\xc0\\xdf\\xc7'"

    The tokens used for form values are Base-64 encodings of the
    names, with the letter 't' prepended to ensure the unnamed utility
    is supported:

    >>> t1.token()
    'tYWJj'
    >>> t2.token()
    'tw4DDn8OH'


    The unnamed utility is given an artificial title for use in user
    interfaces:

    >>> t3 = UtilityNameTerm(u'')
    >>> t3.title()
    u'(unnamed utility)'

    """

    implements(ITokenizedTerm)

    def __init__(self, value):
        self.value = unicode(value)

    def token(self):
        # Return our value as a token.  This is required to be 7-bit
        # printable ascii. We'll use base64 generated from the UTF-8
        # representation.  (The default encoding rules should not be
        # allowed to apply.)
        return "t" + self.value.encode('utf-8').encode('base64')[:-1]

    def title(self):
        return self.value or _("(unnamed utility)")


class UtilityNames:
    """Vocabulary with utility names for a single interface as values.

    >>> class IMyUtility(Interface):
    ...     pass

    >>> class MyUtility(object):
    ...     implements(IMyUtility)

    >>> vocab = UtilityNames(IMyUtility)

    >>> IVocabulary.providedBy(vocab)
    True
    >>> IVocabularyTokenized.providedBy(vocab)
    True

    >>> from zope.app.testing import placelesssetup
    >>> from zope.app.testing import ztapi
    >>> placelesssetup.setUp()

    >>> ztapi.provideUtility(IMyUtility, MyUtility(), 'one')
    >>> ztapi.provideUtility(IMyUtility, MyUtility(), 'two')

    >>> unames = UtilityNames(IMyUtility)
    >>> len(list(unames))
    2
    >>> L = [t.value for t in unames]
    >>> L.sort()
    >>> L
    [u'one', u'two']

    >>> u'one' in vocab
    True
    >>> u'three' in vocab
    False
    >>> ztapi.provideUtility(IMyUtility, MyUtility(), 'three')
    >>> u'three' in vocab
    True

    >>> ztapi.provideUtility(IMyUtility, MyUtility())
    >>> u'' in vocab
    True
    >>> term1 = vocab.getTerm(u'')
    >>> term2 = vocab.getTermByToken(term1.token())
    >>> term2.value
    u''

    >>> placelesssetup.tearDown()
    """

    implements(IVocabularyTokenized)

    def __init__(self, interface):
        self.interface = interface

    def __contains__(self, value):
        return zapi.queryUtility(self.interface, value) is not None

    def getTerm(self, value):
        if value in self:
            return UtilityNameTerm(value)
        raise ValueError(value)

    def getTermByToken(self, token):
        for name, ut in zapi.getUtilitiesFor(self.interface):
            name = unicode(name)
            if token == "t":
                if not name:
                    break
            elif name.encode('utf-8').encode('base64')[:-1] == token:
                break
        else:
            raise LookupError("no matching token: %r" % token)
        return self.getTerm(name)

    def __iter__(self):
        for name, ut in zapi.getUtilitiesFor(self.interface):
            yield UtilityNameTerm(name)

    def __len__(self):
        """Return the number of valid terms, or sys.maxint."""
        return len(list(zapi.getUtilitiesFor(self.interface)))
