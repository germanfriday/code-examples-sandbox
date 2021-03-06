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
"""Browser widgets for sequences

$Id: sequencewidget.py 65777 2006-03-03 18:35:58Z alga $
"""
__docformat__ = 'restructuredtext'

from zope.interface import implements
from zope.i18n import translate

from zope.app import zapi
from zope.app.form.interfaces import IDisplayWidget, IInputWidget
from zope.app.form.interfaces import WidgetInputError, MissingInputError
from zope.app.form import InputWidget
from zope.app.form.browser.widget import BrowserWidget
from zope.app.form.browser.widget import DisplayWidget, renderElement
from zope.app.i18n import ZopeMessageFactory as _

class SequenceWidget(BrowserWidget, InputWidget):
    """A widget baseclass for a sequence of fields.

    subwidget  - Optional CustomWidget used to generate widgets for the
                 items in the sequence
    """

    implements(IInputWidget)

    _type = tuple

    def __init__(self, context, field, request, subwidget=None):
        super(SequenceWidget, self).__init__(context, request)

        self.subwidget = subwidget

    def __call__(self):
        """Render the widget
        """
        assert self.context.value_type is not None

        render = []

        # length of sequence info
        sequence = self._getRenderedValue()
        num_items = len(sequence)
        min_length = self.context.min_length
        max_length = self.context.max_length

        # generate each widget from items in the sequence - adding a
        # "remove" button for each one
        for i in range(num_items):
            value = sequence[i]
            render.append('<tr><td>')
            if num_items > min_length:
                render.append(
                    '<input class="editcheck" type="checkbox" '
                    'name="%s.remove_%d" />' %(self.name, i)
                    )
            widget = self._getWidget(i)
            widget.setRenderedValue(value)
            render.append(widget() + '</td></tr>')

        # possibly generate the "remove" and "add" buttons
        buttons = ''
        if render and num_items > min_length:
            button_label = _('remove-selected-items', "Remove selected items")
            button_label = translate(button_label, context=self.request,
                                     default=button_label)
            buttons += ('<input type="submit" value="%s" name="%s.remove"/>'
                        % (button_label, self.name))
        if max_length is None or num_items < max_length:
            field = self.context.value_type
            button_label = _('Add %s')
            button_label = translate(button_label, context=self.request,
                                     default=button_label)
            button_label = button_label % (field.title or field.__name__)
            buttons += '<input type="submit" name="%s.add" value="%s" />' % (
                self.name, button_label)
        if buttons:
            render.append('<tr><td>%s</td></tr>' % buttons)

        return ('<table border="0">%s</table>\n%s'
                % (''.join(render), self._getPresenceMarker(num_items)))

    def _getWidget(self, i):
        field = self.context.value_type
        if self.subwidget is not None:
            widget = self.subwidget(field, self.request)
        else:
            widget = zapi.getMultiAdapter((field, self.request), IInputWidget)
        widget.setPrefix('%s.%d.'%(self.name, i))
        return widget

    def hidden(self):
        '''Render the list as hidden fields.'''
        # length of sequence info
        sequence = self._getRenderedValue()
        num_items = len(sequence)

        # generate hidden fields for each value
        parts = [self._getPresenceMarker(num_items)]
        for i in range(num_items):
            value = sequence[i]
            widget = self._getWidget(i)
            widget.setRenderedValue(value)
            parts.append(widget.hidden())
        return "\n".join(parts)

    def _getRenderedValue(self):
        if self._renderedValueSet():
            sequence = self._data
        elif self.hasInput():
            sequence = list(self._generateSequence())
        else:
            sequence = []
        # ensure minimum number of items in the form
        if len(sequence) < self.context.min_length:
            sequence = list(sequence)
            for i in range(self.context.min_length - len(sequence)):
                # Shouldn't this use self.field.value_type.missing_value,
                # instead of None?
                sequence.append(None)
        return sequence

    def _getPresenceMarker(self, count=0):
        return ('<input type="hidden" name="%s.count" value="%d" />'
                % (self.name, count))

    def getInputValue(self):
        """Return converted and validated widget data.

        If there is no user input and the field is required, then a
        ``MissingInputError`` will be raised.

        If there is no user input and the field is not required, then
        the field default value will be returned.

        A ``WidgetInputError`` is raised in the case of one or more
        errors encountered, inputting, converting, or validating the data.
        """
        if self.hasInput():
            sequence = self._type(self._generateSequence())
            if sequence != self.context.missing_value:
                self.context.validate(sequence)
            elif self.context.required:
                raise MissingInputError(self.context.__name__,
                                        self.context.title)
            return sequence
        raise MissingInputError(self.context.__name__, self.context.title)

    # TODO: applyChanges isn't reporting "change" correctly (we're
    # re-generating the sequence with every edit, and need to be smarter)
    def applyChanges(self, content):
        field = self.context
        value = self.getInputValue()
        change = field.query(content, self) != value
        if change:
            field.set(content, value)
        return change

    def hasInput(self):
        """Is there input data for the field

        Return ``True`` if there is data and ``False`` otherwise.
        """
        return (self.name + ".count") in self.request.form

    def _generateSequence(self):
        """Take sequence info in the self.request and _data.

        This can only be called if self.hasInput() returns true.
        """
        len_prefix = len(self.name)
        adding = False
        removing = []
        if self.context.value_type is None:
            # Why would this ever happen?
            return []
        # the marker field tells how many individual items were
        # included in the input; we check for exactly that many input
        # widgets
        try:
            count = int(self.request.form[self.name + ".count"])
        except ValueError:
            # could not convert to int; the input was not generated
            # from the widget as implemented here
            raise WidgetInputError(self.context.__name__, self.context.title)

        # pre-populate
        found = {}

        # now look through the request for interesting values
        for i in range(count):
            remove_key = "%s.remove_%d" % (self.name, i)
            if remove_key in self.request.form:
                removing.append(i)
            widget = self._getWidget(i)
            if widget.hasValidInput():
                found[i] = widget.getInputValue()
            else:
                found[i] = None
        adding = (self.name + ".add") in self.request.form

        # remove the indicated indexes
        if (self.name + ".remove") in self.request.form:
            for i in removing:
                del found[i]

        # generate the list, sorting the dict's contents by key
        items = found.items()
        items.sort()
        sequence = [value for key, value in items]

        # add an entry to the list if the add button has been pressed
        if adding:
            # Should this be using self.context.value_type.missing_value
            # instead of None?
            sequence.append(None)

        return sequence

class TupleSequenceWidget(SequenceWidget):
    _type = tuple

class ListSequenceWidget(SequenceWidget):
    _type = list


# Basic display widget

class SequenceDisplayWidget(DisplayWidget):

    _missingValueMessage = _("sequence-value-not-provided",
                             u"(no value available)")

    _emptySequenceMessage = _("sequence-value-is-empty",
                              u"(no values)")

    tag = "ol"
    itemTag = "li"
    cssClass = "sequenceWidget"
    extra = ""

    def __init__(self, context, field, request, subwidget=None):
        super(SequenceDisplayWidget, self).__init__(context, request)
        self.subwidget = subwidget

    def __call__(self):
        # get the data to display:
        if self._renderedValueSet():
            data = self._data
        else:
            data = self.context.get(self.context.context)

        # deal with special cases:
        if data == self.context.missing_value:
            return translate(self._missingValueMessage, self.request)
        data = list(data)
        if not data:
            return translate(self._emptySequenceMessage, self.request)

        parts = []
        for i, item in enumerate(data):
            widget = self._getWidget(i)
            widget.setRenderedValue(item)
            s = widget()
            if self.itemTag:
                s = "<%s>%s</%s>" % (self.itemTag, s, self.itemTag)
            parts.append(s)
        contents = "\n".join(parts)
        if self.tag:
            contents = "\n%s\n" % contents
            contents = renderElement(self.tag,
                                     cssClass=self.cssClass,
                                     extra=self.extra,
                                     contents=contents)
        return contents

    def _getWidget(self, i):
        field = self.context.value_type
        if self.subwidget is not None:
            widget = self.subwidget(field, self.request)
        else:
            widget = zapi.getMultiAdapter(
                (field, self.request), IDisplayWidget)
        widget.setPrefix('%s.%d.'%(self.name, i))
        return widget
