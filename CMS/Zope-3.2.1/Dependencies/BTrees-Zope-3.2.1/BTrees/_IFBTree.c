/*############################################################################
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
############################################################################*/

#define MASTER_ID "$Id: _IFBTree.c 28613 2004-12-09 21:02:35Z tim_one $\n"

/* IIBTree - int key, int value BTree

   Implements a collection using int type keys
   and int type values
*/

/* Setup template macros */

#define PERSISTENT

#define MOD_NAME_PREFIX "IF"
#define INITMODULE init_IFBTree
#define DEFAULT_MAX_BUCKET_SIZE 120
#define DEFAULT_MAX_BTREE_SIZE 500

#include "intkeymacros.h"
#include "floatvaluemacros.h"
#include "BTreeModuleTemplate.c"
