##############################################################################
#
# Copyright (c) 2002 Zope Corporation and Contributors.
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
"""Resource URL acess

$Id: resources.py 29143 2005-02-14 22:43:16Z srichter $
"""
from zope.app.publisher.browser import BrowserView
from zope.publisher.interfaces.browser import IBrowserPublisher
from zope.publisher.interfaces import NotFound
from zope.interface import implements

from zope.app import zapi
from zope.app.location import locate

class Resources(BrowserView):
    """Provide a URL-accessible resource namespace
    """

    implements(IBrowserPublisher)

    def publishTraverse(self, request, name):
        '''See interface IBrowserPublisher'''

        resource = zapi.queryAdapter(request, name=name)
        if resource is None:
            raise NotFound(self, name)

        sm = zapi.getSiteManager()
        locate(resource, sm, name)
        return resource

    def browserDefault(self, request):
        '''See IBrowserPublisher'''
        return empty, ()

    def __getitem__(self, name):
        return self.publishTraverse(self.request, name)


def empty():
    return ''
