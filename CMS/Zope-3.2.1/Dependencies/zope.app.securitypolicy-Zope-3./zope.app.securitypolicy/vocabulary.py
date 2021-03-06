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
"""Role Id Vocabulary.

This vocabulary provides role IDs.

$Id: vocabulary.py 29143 2005-02-14 22:43:16Z srichter $
"""
from zope.interface import implements
from zope.app import zapi
from zope.schema.vocabulary import SimpleTerm, SimpleVocabulary
from zope.app.securitypolicy.interfaces import IRole
from zope.app.securitypolicy.interfaces import IGrantVocabulary

class RoleIdsVocabulary(SimpleVocabulary):
    """A vocabular of role IDs.

    Term values are the role ID strings
    Term are stored by title

    To illustrate, we need to register the role IDs vocab:

        >>> from zope.app.testing.placelesssetup import setUp, tearDown
        >>> setUp()
        >>> from zope.schema.vocabulary import getVocabularyRegistry
        >>> registry = getVocabularyRegistry()
        >>> registry.register('Role Ids', RoleIdsVocabulary)

    Let's register some sample roles to test against them

       >>> from zope.app.securitypolicy.interfaces import IRole
       >>> from zope.app.securitypolicy.role import Role
       >>> from zope.app.testing import ztapi
       >>> ztapi.provideUtility(IRole, Role('a_id','a_title'), 'a_id')
       >>> ztapi.provideUtility(IRole, Role('b_id','b_title'), 'b_id')

    Let's lookup the roles using the vocabulary

       >>> vocab = registry.get(None, 'Role Ids')

       >>> vocab.getTermByToken('a_id').value
       u'a_id'
       >>> vocab.getTermByToken('b_id').value
       u'b_id'

       >>> tearDown()

    """

    def __init__(self, context):
        terms = []
        roles = zapi.getUtilitiesFor(IRole, context)
        for name, role in roles:
            terms.append(SimpleTerm(name, name, name))
        super(RoleIdsVocabulary, self).__init__(terms)


class GrantVocabulary(SimpleVocabulary):
    """A vocabular for getting the RadioWidget via the Choice field."""
    
    implements(IGrantVocabulary)


