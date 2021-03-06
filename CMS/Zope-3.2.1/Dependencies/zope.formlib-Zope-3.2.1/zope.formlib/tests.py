##############################################################################
#
# Copyright (c) 2005 Zope Corporation and Contributors. All Rights Reserved.
#
# This software is subject to the provisions of the Zope Public License,
# Version 2.1 (ZPL).  A copy of the ZPL should accompany this distribution.
# THIS SOFTWARE IS PROVIDED "AS IS" AND ANY AND ALL EXPRESS OR IMPLIED
# WARRANTIES ARE DISCLAIMED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
# WARRANTIES OF TITLE, MERCHANTABILITY, AGAINST INFRINGEMENT, AND FITNESS
# FOR A PARTICULAR PURPOSE.
#
##############################################################################
"""

$Id: tests.py 40053 2005-11-11 22:01:20Z jim $
"""

import unittest
import pytz
from zope import component, interface
import zope.interface.common.idatetime
import zope.i18n
import zope.publisher.interfaces
import zope.publisher.interfaces.browser
import zope.schema.interfaces
import zope.app.form.browser
import zope.app.form.browser.exception
import zope.app.form.browser.interfaces
import zope.app.form.interfaces
from zope.app.testing import placelesssetup
import zope.app.traversing.adapters

from zope.formlib import interfaces, namedtemplate, form

@interface.implementer(zope.interface.common.idatetime.ITZInfo)
@component.adapter(zope.publisher.interfaces.IRequest)
def requestToTZInfo(request):
    return pytz.timezone('US/Hawaii')

def pageSetUp(test):
    placelesssetup.setUp(test)
    component.provideAdapter(
        zope.app.traversing.adapters.DefaultTraversable,
        [None],
        )

@component.adapter(interfaces.IForm)
@namedtemplate.NamedTemplateImplementation
def TestTemplate(self):
    status = self.status
    if status:
        status = zope.i18n.translate(status,
                                     context=self.request,
                                     default=self.status)
        if getattr(status, 'mapping', 0):
            status = zope.i18n.interpolate(status, status.mapping)
        print status

    result = []

    if self.errors:
        for error in self.errors:
            result.append("%s: %s" % (error.__class__.__name__, error))

    for w in self.widgets:
        result.append(w())
        error = w.error()
        if error:
            result.append(str(error))

    for action in self.availableActions():
        result.append(action.render())

    return '\n'.join(result)

def formSetUp(test):
    placelesssetup.setUp(test)
    component.provideAdapter(
        zope.app.form.browser.TextWidget,
        [zope.schema.interfaces.ITextLine,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IInputWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.FloatWidget,
        [zope.schema.interfaces.IFloat,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IInputWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.UnicodeDisplayWidget,
        [zope.schema.interfaces.IInt,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IDisplayWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.IntWidget,
        [zope.schema.interfaces.IInt,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IInputWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.UnicodeDisplayWidget,
        [zope.schema.interfaces.IFloat,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IDisplayWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.UnicodeDisplayWidget,
        [zope.schema.interfaces.ITextLine,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IDisplayWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.DatetimeDisplayWidget,
        [zope.schema.interfaces.IDatetime,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.interfaces.IDisplayWidget,
        )
    component.provideAdapter(
        zope.app.form.browser.exception.WidgetInputErrorView,
        [zope.app.form.interfaces.IWidgetInputError,
         zope.publisher.interfaces.browser.IBrowserRequest,
         ],
        zope.app.form.browser.interfaces.IWidgetInputErrorView,
        )
    component.provideAdapter(TestTemplate, name='default')
    component.provideAdapter(requestToTZInfo)
    component.provideAdapter(form.render_submit_button, name='render')

def makeSureRenderCanBeCalledWithoutCallingUpdate():
    """\

    >>> from zope.formlib import form
    >>> from zope import interface, schema
    >>> class IOrder(interface.Interface):
    ...     identifier = schema.Int(title=u"Identifier", readonly=True)
    ...     name = schema.TextLine(title=u"Name")
    ...     min_size = schema.Float(title=u"Minimum size")
    ...     max_size = schema.Float(title=u"Maximum size")
    ...     now = schema.Datetime(title=u"Now", readonly=True)

    >>> class MyForm(form.EditForm):
    ...     form_fields = form.fields(IOrder, keep_readonly=['identifier'])

    >>> class Order:
    ...     interface.implements(IOrder)
    ...     identifier = 1
    ...     name = 'unknown'
    ...     min_size = 1.0
    ...     max_size = 10.0

    >>> from zope.publisher.browser import TestRequest

    >>> myform = MyForm(Order(), TestRequest())
    >>> print myform.render() # doctest: +NORMALIZE_WHITESPACE
    1
    <input class="textType" id="form.name" name="form.name"
           size="20" type="text" value="unknown"  />
    <input class="textType" id="form.min_size" name="form.min_size"
           size="10" type="text" value="1.0"  />
    <input class="textType" id="form.max_size" name="form.max_size"
           size="10" type="text" value="10.0"  />
    <input type="submit" id="form.actions.apply" name="form.actions.apply"
           value="Apply" class="button" />

"""

def make_sure_i18n_is_called_correctly_for_actions():
    """\

We want to make sure that i18n is called correctly.  This is in
response to a bug that occurred because actions called i18n.translate
with incorrect positional arguments.

We'll start by setting up an action:

    >>> import zope.i18nmessageid
    >>> _ = zope.i18nmessageid.MessageFactory('my.domain')
    >>> action = form.Action(_("MyAction"))

Actions get bound to forms.  We'll set up a test request, create a
form for it and bind the action to the form:

    >>> myform = form.FormBase(None, 42)
    >>> action = action.__get__(myform)

Button labels are rendered by form.render_submit_button, passing the
bound action.  Before we call this however, we need to set up a dummy
translation domain.  We'll create one for our needs:

    >>> import zope.i18n.interfaces
    >>> class MyDomain:
    ...     interface.implements(zope.i18n.interfaces.ITranslationDomain)
    ...
    ...     def translate(self, msgid, mapping=None, context=None,
    ...                   target_language=None, default=None):
    ...         print msgid
    ...         print mapping
    ...         print context
    ...         print target_language
    ...         print default
    ...         return msgid

    >>> component.provideUtility(MyDomain(), name='my.domain')

Now, if we call render_submit_button, we should be able to verify the
data passed to translate:

    >>> form.render_submit_button(action)() # doctest: +NORMALIZE_WHITESPACE
    MyAction
    None
    42
    None
    MyAction
    u'<input type="submit" id="form.actions.myaction"
       name="form.actions.myaction" value="MyAction" class="button" />'


"""

def test_form_template_i18n():
    """\
Let's try to check that the formlib templates handle i18n correctly.
We'll define a simple form:

    >>> from zope.app.pagetemplate import ViewPageTemplateFile
    >>> import zope.i18nmessageid
    >>> _ = zope.i18nmessageid.MessageFactory('my.domain')

    >>> from zope import schema
    >>> class MyForm(form.Form):
    ...     label = _('The label')
    ...     status = _('Success!')
    ...     form_fields = form.Fields(
    ...         schema.TextLine(__name__='name',
    ...                         title=_("Name"),
    ...                         description=_("Enter your name"),
    ...                         ),
    ...         )
    ...     @form.action(_('Ok'))
    ...     def ok(self, action, data):
    ...         pass
    ...     page = ViewPageTemplateFile("pageform.pt")
    ...     subpage = ViewPageTemplateFile("subpageform.pt")

Now, we should be able to create a form instance:

    >>> from zope.publisher.browser import TestRequest
    >>> request = TestRequest()
    >>> form = MyForm(object(), request)

Unfortunately, the "page" template uses a page macro. We need to
provide a template that it can get one from.  Here, we'll set up a
view that provides the necessary macros:

    >>> from zope.pagetemplate.pagetemplate import PageTemplate
    >>> macro_template = PageTemplate()
    >>> macro_template.write('''\
    ... <html metal:define-macro="view">
    ... <body metal:define-slot="body" />
    ... </html>
    ... ''')
    
We also need to provide a traversal adapter for the view namespace
that lets us look up the macros.

    >>> import zope.app.traversing.interfaces
    >>> class view:
    ...     component.adapts(None, None)
    ...     interface.implements(zope.app.traversing.interfaces.ITraversable)
    ...     def __init__(self, ob, r=None):
    ...         pass
    ...     def traverse(*args):
    ...         return macro_template.macros

    >>> component.provideAdapter(view, name='view')

And we have to register the default traversable adapter (I wish we had
push templates):

    >>> from zope.app.traversing.adapters import DefaultTraversable
    >>> component.provideAdapter(DefaultTraversable, [None])

We need to set up the translation framework. We'll just provide a
negotiator that always decides to use the test language:

    >>> import zope.i18n.interfaces
    >>> class Negotiator:
    ...     interface.implements(zope.i18n.interfaces.INegotiator)
    ...     def getLanguage(*ignored):
    ...         return 'test'

    >>> component.provideUtility(Negotiator())

And we'll set up the fallback-domain factory, which provides the test
language for all domains:

    >>> from zope.i18n.testmessagecatalog import TestMessageFallbackDomain
    >>> component.provideUtility(TestMessageFallbackDomain)
    
OK, so let's see what the page form looks like. First, we'll compute
the page:

    >>> form.update()
    >>> page = form.page()

We want to make sure that the page has the translations we expect and
that it doesn't double translate anything.  We'll write a generator
that extracts the translations, complaining if any are nested:

    >>> def find_translations(text):
    ...     l = 0
    ...     while 1:
    ...         lopen = text.find('[[', l)
    ...         lclose = text.find(']]', l)
    ...         if lclose >= 0 and lclose < lopen:
    ...             raise ValueError(lopen, lclose, text)
    ...         if lopen < 0:
    ...             break
    ...         l = lopen + 2
    ...         lopen = text.find('[[', l)
    ...         lclose = text.find(']]', l)
    ...         if lopen >= 0 and lopen < lclose:
    ...             raise ValueError(lopen, lclose, text)
    ...         if lclose < 0:
    ...             raise ValueError(l, text)
    ...         yield text[l-2:lclose+2]
    ...         l = lclose + 2

    >>> for t in find_translations(page):
    ...     print t
    [[my.domain][The label]]
    [[my.domain][Success!]]
    [[my.domain][Name]]
    [[my.domain][Enter your name]]
    [[my.domain][Ok]]

Now, let's try the same thing with the sub-page form:

    >>> for t in find_translations(form.subpage()):
    ...     print t
    [[my.domain][The label]]
    [[my.domain][Success!]]
    [[my.domain][Name]]
    [[my.domain][Enter your name]]
    [[my.domain][Ok]]


































"""

def test_suite():
    from zope.testing import doctest
    return unittest.TestSuite((
        doctest.DocFileSuite(
            'page.txt',
            setUp=pageSetUp, tearDown=placelesssetup.tearDown,
            ),
        doctest.DocFileSuite(
            'form.txt',
            setUp=formSetUp, tearDown=placelesssetup.tearDown,
            ),
        doctest.DocTestSuite(
            setUp=formSetUp, tearDown=placelesssetup.tearDown,
            ),
        doctest.DocFileSuite(
            'namedtemplate.txt',
            setUp=pageSetUp, tearDown=placelesssetup.tearDown,
            ),
        ))

if __name__ == '__main__':
    unittest.main(defaultTest='test_suite')

