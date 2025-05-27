var config = {
	'.chosen-select'           : {},
}
for (var selector in config) {
	$(selector).chosen(config[selector]);
}
