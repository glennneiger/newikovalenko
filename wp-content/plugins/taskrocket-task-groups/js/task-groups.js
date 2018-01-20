$(document).ready(function() {
    $(".post-type-task_groups #pageparentdiv span:contains('Attributes')").html("Belongs in this Task Group:"), 
    $(".post-type-task_groups #more_info").hide();
    var t = jQuery("#parent_id");
    this.value;
    t.change(function() {
        "" == $(this).val() ? $("#more_info").fadeOut() : $("#more_info").fadeIn()
    }), $("#parent_id").val() && $("#more_info").show();
    //$(".post-type-task_groups .postbox .level-1, .post-type-task_groups .postbox .level-2, .post-type-task_groups .postbox .level-3, .post-type-task_groups .postbox .level-4").remove();
    //$(".post-type-task_groups .start-date, .post-type-task_groups .due-date, .post-type-task_groups .attached").remove();
    //$(".post-type-task_groups #post_author_override").remove();
    $(".post-type-task_groups .post-type-task_groups .attached").remove();
});