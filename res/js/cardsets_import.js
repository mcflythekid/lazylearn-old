$("textarea#text").keydown(function(e) {
	var keyCode = e.keyCode || e.which;

	if (keyCode == 9) {
		var start = this.selectionStart;
		var end = this.selectionEnd;

		var $this = $(this);
		var value = $this.val();

		$this.val(value.substring(0, start)
					+ "\t"
					+ value.substring(end));

		this.selectionStart = this.selectionEnd = start + 1;

		e.preventDefault();
	}
});
