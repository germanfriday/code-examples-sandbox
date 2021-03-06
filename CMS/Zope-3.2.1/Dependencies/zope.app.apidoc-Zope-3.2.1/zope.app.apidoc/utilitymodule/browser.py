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
"""Utility Module Views

$Id: browser.py 39722 2005-10-29 23:25:40Z srichter $
"""
__docformat__ = 'restructuredtext'

from zope.security.proxy import removeSecurityProxy

from zope.app import zapi
from zope.app.location import LocationProxy
from zope.app.apidoc.ifacemodule.browser import InterfaceDetails
from zope.app.apidoc.component import getUtilityInfoDictionary
from zope.app.apidoc.utilities import getPythonPath
from zope.app.apidoc.utilitymodule.utilitymodule import NONAME, Utility
from zope.app.apidoc.utilitymodule.utilitymodule import UtilityInterface

class UtilityDetails(object):
    """Utility Details View."""

    def getName(self):
        """Get the name of the utility."""
        name = self.context.name
        if name == NONAME:
            return 'no name'
        return name

    def getInterface(self):
        """Return the interface the utility provides."""
        schema = LocationProxy(self.context.interface,
                               self.context,
                               getPythonPath(self.context.interface))
        details = InterfaceDetails(schema, self.request)
        return details

    def getComponent(self):
        """Return the python path of the implementation class."""
        # Remove proxy here, so that we can determine the type correctly
        naked = removeSecurityProxy(self.context.registration)
        result = getUtilityInfoDictionary(naked)
        return {'path': result['path'], 'url': result['url']}


class Menu(object):
    """Menu View Helper Class"""

    def getMenuTitle(self, node):
        """Return the title of the node that is displayed in the menu."""
        obj = node.context
        if zapi.isinstance(obj, UtilityInterface):
            return zapi.name(obj).split('.')[-1]
        if obj.name == NONAME:
            return 'no name'
        return obj.name

    def getMenuLink(self, node):
        """Return the HTML link of the node that is displayed in the menu."""
        obj = node.context
        if zapi.isinstance(obj, Utility):
            iface = zapi.getParent(obj)
            return './'+zapi.name(iface) + '/' + zapi.name(obj) + '/index.html'
        if zapi.isinstance(obj, UtilityInterface):
            return '../Interface/'+zapi.name(obj) + '/index.html'
        return None
