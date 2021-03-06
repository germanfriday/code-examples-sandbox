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
"""Browser views

$Id: __init__.py 25177 2004-06-02 13:17:31Z jim $
"""

from zope.app import zapi
from zope.app.publisher.browser import BrowserView

from zope.app.tree.interfaces import ITreeStateEncoder
from zope.app.tree.node import Node

class StatefulTreeView(BrowserView):

    def statefulTree(self, root=None, filter=None, tree_state=None):
        """Build a tree with tree state information from a request.
        """
        if root is None:
            root = self.context
        expanded_nodes = []
        if tree_state is not None:
            encoder = zapi.getUtility(ITreeStateEncoder)
            expanded_nodes = encoder.decodeTreeState(tree_state)
        node = Node(root, expanded_nodes, filter)
        node.expand()
        return node
