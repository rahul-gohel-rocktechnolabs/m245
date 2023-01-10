require(['jquery','Magento_Ui/js/modal/modal'],function($,modal){
	
	var options = {
		type: 'popup',
		responsive: true,
		innerScroll: true,
		title: 'Write Answer',
		buttons: [{
			text: $.mage.__('Cancel'),
			class: '',
			click: function () {
				this.closeModal();
			}
		},{
			text: $.mage.__('Submit Answer'),
			class: 'action submit primary',
			click: function () {
				thismodel = this;
				var form = $(".question-answer-form")
				var url = form.attr("action");
			
				$.ajax({
					url:url,
					data:form.serialize(),
					dataType: "JSON",
					type:"post",
					showLoader: true,
					success:function(data){
						var div  = $("<div>");
						var msg =  $("<div>");
						
						if(data.status)
						{
							div.addClass("message-success success message")
						}
						else
						{
							div.addClass("message-success error message")
						}
						
						msg.html(data.message);
						
						div .html(msg);
						
						form.parents("#product-question-answer").find("#answer-message").html(div);
						form.parents("#product-question-answer").find("#answer-message").find(".message").fadeOut(15000);
						
						if(data.status)
						{
							var asn_ul = $("#answers_answer_value_container .answers ul");
							var ans_li = form.find("ansli").first().clone(true,true);
							ans_li.find('.anstitle').html(data.answer.answer)
							ans_li.find('.name span').html(data.answer.name)
							ans_li.find('.date span').html(data.answer.date)
							ans_li.find('.status span').html(data.answer.statue)
							ans_li.find('.statuschange a').attr("href",url);
							asn_ul.prepend(ans_li);
							form.find("input,textarea").val('');
							bindEvents();
						}
						setTimeout(function(){thismodel.closeModal()},1000);
					}
				})
			}
		}]
	};

	var popup = modal(options, $('#product-question-answer'));
	$(".answerbutton").on("click",function(){
		$('#product-question-answer').modal('openModal');
		return false;
	})
	
	function bindEvents()
	{	
			$(".statuschange a").off();
			$(".statuschange a").on("click",function(){
				var curr = $(this);
				
				var url = curr.attr("href");
				$('[data-role="answerpannel"]').trigger('show.loader');
				
				$.ajax({
					url:url,
					type:"get",
					dataType: "JSON",
					showLoader: true, // enable loader
					success:function(data){
						
						$('[data-role="answerpannel"]').trigger('hide.loader'); 
						
						var div  = $("<div>");
						var msg =  $("<div>");
						
						if(data.status == 1)
						{
							div.addClass("message-success success message")
							curr.attr("href",data.url);
							curr.html(data.status_text);
							curr.parents('li').find(".current-status").html(data.prev_status_text)
						}
						else
						{
							div.addClass("message-success error message")
						}
						
						msg.html(data.message);
						
						div .html(msg);
						
						$("#answer-message").html(div)
						
						$("#answer-message").find(".message").fadeOut(15000);
						
					}
				});
				
				return false;
			})
		
		var qa_curr_page = 1;
		
	}
	bindEvents();
})