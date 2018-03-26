/**
 * Method for open modal pop-up of reply comment form
 *
 * @param  int  modalId    Modal ID
 * @param  int  commentId  Comment ID for reply to
 * @param  int  private    Private mode. (1 => private. 0 => public)
 *
 * @return  void
 */
function reditemCommentReply(modalId, commentId, private)
{
	(function($){
		var modal = $("#reditemReplyModal" + modalId);
		var replyForm = modal.find("form");
		replyForm[0].reset();
		replyForm.find("#replyComment" + modalId).attr("name", "comment");
		replyForm.find(".reditemReplyModalParentId").val(commentId);

		if (private == 1)
		{
			replyForm.find('input[name="private"]').prop("disabled", true).parent().addClass('hide');
		}
		else
		{
			replyForm.find('input[name="private"]').parent().removeClass('hide');
		}

		modal.find("#reditemReplyModalSubmit" + modalId).click(function(){
			replyForm.submit();
		});
	})(jQuery);
}

/**
 * Method for open modal pop-up of edit comment form
 *
 * @param  int      modalId       Modal ID
 * @param  int      commentId     Comment ID for edit
 * @param  string   modalTitle    Modal title
 * @param  comment  comment       Old comment content
 *
 * @return  void
 */
function reditemCommentEdit(modalId, commentId, modalTitle, comment)
{
	(function($){
		var modal = $("#reditemReplyModal" + modalId);
		modal.find('.modal-header h2').text(modalTitle);

		var replyForm = modal.find("form");
		replyForm[0].reset();
		replyForm.find("#replyComment" + modalId).attr("name", "comment").text(comment);

		if (typeof tinyMCE != "undefined") {
			var editor = tinyMCE.get("replyComment" + modalId);
			editor.setContent(comment);
			editor.isNotDirty = true;
			editor.nodeChanged();
		}
		else {
			$("#replyComment" + modalId).val(comment);
		}

		replyForm.find('input[name="id"]').val(commentId);

		modal.find("#reditemReplyModalSubmit" + modalId).click(function(){
			replyForm.submit();
		});
	})(jQuery);
}

/**
 * Method for user delete his/her comment
 *
 * @param  int  commentId   Comment ID
 *
 * @return  void
 */
function reditemCommentUserDelete(commentId)
{
	(function($){
		var url = 'index.php?option=com_reditem&view=comment&task=comment.ajaxDeleteComment&id=' + commentId;
		$.ajax({
			url: url,
			dataType: "json",
			cache: false
		})
		.error(function (data) {
			console.log('reditemCommentUserDelete > Error: ' + data);
		})
		.done(function (data){
			if (data.status == 1)
			{
				var commentDiv = $('#reditemCommentContent_' + commentId);
				commentDiv.html(data.msg);
				commentDiv.parent().find('.comment-tools').remove();
			}
			else
			{
				alert(data.msg);
			}
		});
	})(jQuery);
}