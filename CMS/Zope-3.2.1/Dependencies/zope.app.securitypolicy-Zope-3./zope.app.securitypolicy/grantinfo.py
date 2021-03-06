##############################################################################
#
# Copyright (c) 2004 Zope Corporation and Contributors.
# All Rights Reserved.
#
# This software is subject to the provisions of the Zope Public License,
# Version 2.0 (ZPL).  A copy of the ZPL should accompany this distribution.
# THIS SOFTWARE IS PROVIDED "AS IS" AND ANY AND ALL EXPRESS OR IMPLIED
# WARRANTIES ARE DISCLAIMED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF TITLE, MERCHANTABILITY, AGAINST INFRINGEMENT, AND FITNESS
# FOR A PARTICULAR PURPOSE.
#
##############################################################################
"""Grant info

$Id: grantinfo.py 28438 2004-11-11 17:14:33Z jim $
"""
from zope.app.security.settings import Unset

from zope.app.securitypolicy.interfaces import IGrantInfo

from zope.app.annotation.interfaces import IAnnotations

from zope.app.securitypolicy.principalpermission \
     import AnnotationPrincipalPermissionManager
prinperkey = AnnotationPrincipalPermissionManager.key
del AnnotationPrincipalPermissionManager

from zope.app.securitypolicy.principalrole \
     import AnnotationPrincipalRoleManager
prinrolekey = AnnotationPrincipalRoleManager.key
del AnnotationPrincipalRoleManager

from zope.app.securitypolicy.rolepermission \
     import AnnotationRolePermissionManager
rolepermkey = AnnotationRolePermissionManager.key
del AnnotationRolePermissionManager



class AnnotationGrantInfo(object):

    prinper = prinrole = permrole = {}

    def __init__(self, context):
        self._context = context
        annotations = IAnnotations(context, None)
        if annotations is not None:

            prinper = annotations.get(prinperkey)
            if prinper is not None:
                self.prinper = prinper._bycol # by principals

            prinrole = annotations.get(prinrolekey)
            if prinrole is not None:
                self.prinrole = prinrole._bycol # by principals

            roleper = annotations.get(rolepermkey)
            if roleper is not None:
                self.permrole = roleper._byrow # by permission
            
    def __nonzero__(self):
        return bool(self.prinper or self.prinrole or self.permrole)

    def principalPermissionGrant(self, principal, permission):
        prinper = self.prinper.get(principal)
        if prinper:
            return prinper.get(permission, Unset)
        return Unset

    def getRolesForPermission(self, permission):
        permrole = self.permrole.get(permission)
        if permrole:
            return permrole.items()
        return ()

    def getRolesForPrincipal(self, principal):
        prinrole = self.prinrole.get(principal)
        if prinrole:
            return prinrole.items()
        return ()
