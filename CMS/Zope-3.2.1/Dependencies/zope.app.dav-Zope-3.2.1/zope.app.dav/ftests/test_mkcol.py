##############################################################################
#
# Copyright (c) 2003 Zope Corporation and Contributors.
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
"""Functional tests for MKCOL.

$Id: test_mkcol.py 29703 2005-03-28 19:26:23Z tim_one $
"""
import unittest

from zope.app.dav.ftests.dav import DAVTestCase
import transaction

class TestMKCOL(DAVTestCase):

    def test_mkcol_not_folderish(self):
        self.addPage('/bar/pt', u'<span />')
        transaction.commit()
        self.verifyStatus(path='/bar/pt/foo', body='', basic='mgr:mgrpw',
                          expected=404)

    def test_mkcol_not_folderish_existing(self):
        self.addPage('/bar/pt', u'<span />')
        transaction.commit()
        self.verifyStatus(path='/bar/pt', body='', basic='mgr:mgrpw',
                          expected=405)

    def test_mkcol_not_existing(self):
        self.verifyStatus(path='/mkcol_test', body='', basic='mgr:mgrpw',
                          expected=201)

    def test_mkcol_parent_not_existing(self):
        self.verifyStatus(path='/bar/mkcol_test', body='', basic='mgr:mgrpw',
                          expected=409)

    def test_mkcol_existing(self):
        self.createFolders('/bar/mkcol_test')
        transaction.commit()
        self.verifyStatus(path='/bar', body='', basic='mgr:mgrpw',
                          expected=405)

    def test_mkcol_with_body(self):
        self.verifyStatus(path='/mkcol_test', body='bla', basic='mgr:mgrpw',
                          expected=415)

    def verifyStatus(self, path, body, basic, expected=201):
        clen = len(body)
        result = self.publish(path, basic, env={'REQUEST_METHOD':'MKCOL',
                                                'CONTENT-LENGHT': clen},
                              request_body=body, handle_errors=True)
        self.assertEquals(result.getStatus(), expected)

def test_suite():
    suite = unittest.TestSuite()
    suite.addTest(unittest.makeSuite(TestMKCOL))
    return suite


if __name__ == '__main__':
    unittest.main()
