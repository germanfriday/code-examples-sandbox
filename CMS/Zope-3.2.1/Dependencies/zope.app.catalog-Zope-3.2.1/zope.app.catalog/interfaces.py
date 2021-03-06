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
"""Catalog Interfaces

$Id: interfaces.py 39064 2005-10-11 18:40:10Z philikon $
"""

import zope.index.interfaces
import zope.interface
import zope.schema
import zope.app.container.interfaces
import zope.app.container.constraints

from zope.app.i18n import ZopeMessageFactory as _

class ICatalogQuery(zope.interface.Interface):
    """Provides Catalog Queries."""

    def searchResults(**kw):
        """Search on the given indexes."""


class ICatalogEdit(zope.index.interfaces.IInjection):
    """Allows one to manipulate the Catalog information."""

    def updateIndexes():
        """Reindex all objects."""


class ICatalogIndex(zope.index.interfaces.IInjection,
                    zope.index.interfaces.IIndexSearch,
                    ):
    """An index to be used in a catalog
    """
    __parent__ = zope.schema.Field()
    

class ICatalog(ICatalogQuery, ICatalogEdit,
               zope.app.container.interfaces.IContainer): 
    """Marker to describe a catalog in content space."""

    zope.app.container.constraints.contains(ICatalogIndex)

ICatalogIndex['__parent__'].constraint = (
    zope.app.container.constraints.ContainerTypesConstraint(ICatalog))

class IAttributeIndex(zope.interface.Interface):
    """I index objects by first adapting them to an interface, then
       retrieving a field on the adapted object.
    """

    interface = zope.schema.Choice(
        title=_(u"Interface"),
        description=_(u"Objects will be adapted to this interface"),
        vocabulary="Interfaces",
        required=False,
        )

    field_name = zope.schema.BytesLine(
        title=_(u"Field Name"),
        description=_(u"Name of the field to index"),
        )

    field_callable = zope.schema.Bool(
        title=_(u"Field Callable"),
        description=_(u"If true, then the field should be called to get the "
                      u"value to be indexed"),
        )
        
