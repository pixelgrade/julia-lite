# select2v4-sortable
Modified version of [select2-sortable](https://github.com/vafour/select2-sortable), a little plugin to make [select2](https://github.com/select2/select2) v4.x multiple select become sortable out of the box.

Usage:
```js
	// init select2 sortable
	$(select2multiselect).select2Sortable();

	// destroy select2 sortable
	$(select2multiselect).select2Sortable('destroy');

	// manually trigger the sorting
	$(select2multiselect).select2SortableOrder();

	// custom options
	$(select2multiselect).select2Sortable({
	  bindOrder: 'formSubmit' // or `sortableStop`,
	  sortableOptions: {
		// please refer to jQuery UI sortable API (http://api.jqueryui.com/sortable/)
	  }
	});
```
