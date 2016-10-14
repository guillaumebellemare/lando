<script>
jQuery.validator.addMethod("cdnPostal", function(postal, element) {
    return this.optional(element) || 
    postal.match(/[a-zA-Z][0-9][a-zA-Z](-| |)[0-9][a-zA-Z][0-9]/);
}, "<?=$validate['zip'];?>");
</script>
