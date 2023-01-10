require(['jquery'],function($){
	$(".box-addanswer #product-askquestion-button").on("click",function(){
		var ww = jQuery(window).width()
		if(ww>520)
		{
			var topf = jQuery("#question-form").offset().top;
			jQuery(window).scrollTop(topf);
		}
	})
	$("#question-form").on("submit",function(){
		var form = $(this)
		var url = form.attr("action");
		
		$("#questionqa .fotorama__spinner").show()
		$.ajax({
			url:url,
			data:form.serialize(),
			dataType: "JSON",
			type:"post",
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
				
				$("#question-message").html(div)
				
				$("#question-message").find(".message").fadeOut(15000);
				
				if(data.status)
				{
					jQuery(".question-form").slideUp()
					form.find("textarea,#captcha_qa_captcha_form_1").val('')
				}
				$("#captcha-container-qa_captcha_form_1 .captcha-reload").click()
				$("#questionqa .fotorama__spinner").hide()
			}
		})
		
		return false;
	})
	
	function bindEvents()
	{
		$("#questionqa .actions .action").unbind().click(function(){
			var action = $(this);
			var url = action.attr("href");
			$.ajax({
				url:url,
				type:"get",
				dataType:"json",
				success:function(data){
					
					if(data.status==1)
					{
						action.find(".count#"+data.id).html(data.count)
						if(data.likes){
							if(data.object == 'question'){
								$('#productquestions .qheader .like .count#'+data.id).html(data.likes)
							}
							else{
								$('#productquestions .ansheader .like .count#'+data.id).html(data.likes)
								
							}
							
						}
						else{
							if(data.object == 'question'){
								$('#productquestions .qheader .dislike .count#'+data.id).html(data.dislikes)
							}
							else{
								$('#productquestions .ansheader .dislike .count#'+data.id).html(data.dislikes)
								
							}						
						}
					}
					else
					{
						var div  = $("<div>");
						var msg =  $("<div>");
						
						div.addClass("message-success error message")
					
						msg.html(data.message);
						
						div .html(msg);
						var message = action.parents(".productquestion").find(".messages")
						message.html(div);
						message.html(div).find("div").fadeOut(15000);
						
					}
					
				}
			})
			return false
		})
		$(".question-answer-form").off();
		$(".question-answer-form").on("submit",function(){
			var form = $(this)
			var url = form.attr("action");
			
			$("#questionqa .fotorama__spinner").show()
			$.ajax({
				url:url,
				data:form.serialize(),
				dataType: "JSON",
				type:"post",
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
					
					form.parents(".productquestion").find("#answer-message").html(div);
					form.parents(".productquestion").find("#answer-message").find(".message").fadeOut(15000);
					
					if(data.status)
					{
						form.parents(".productquestion").find(".answer-form").slideUp()
						form.find("textarea").val('')
					}
					$("#questionqa .fotorama__spinner").hide()
				}
			})
			
			return false;
		})
		
		$(".loadmoreanswerbutton").off()
		$(".loadmoreanswerbutton").on("click",function(){
				$("#questionqa .fotorama__spinner").show()
				var url =$(this).data("url");
				var curr = $(this)
				$.ajax({
					url:url,
					success:function(data){
						
						curr.parents(".productquestion").find(".answers").html(data)
						
						$("#questionqa .fotorama__spinner").hide()
						
						bindEvents();
					}
				})
				return false;
		})
		
	}
	function bindLodaMoreQuestion(){
			
		$(".loadmorequestionbutton").off()
		$(".loadmorequestionbutton").on("click",function(){
			var qa_total_page = $(this).data('totalpage');
			
			$("#questionqa .fotorama__spinner").show()
			
			if(qa_curr_page <= qa_total_page)
			{
				var url =$("#qasort").val()+"/page/"+(++qa_curr_page)+"/?q="+$("#qaq").val();
				
				var curr = $(this)
				
				$.ajax({
					url:url,
					success:function(data){
						
						$(".loadmorequestion").append($(data).find("#productquestions"))
						
						$("#questionqa .fotorama__spinner").hide()
						
						if(qa_curr_page > qa_total_page)
						{
							curr.parents(".fieldset").hide();
						}
						
						bindEvents();
					}
				})
				
			}
			
			return false;
		})
		
	}
	bindLodaMoreQuestion();
	
	$("#qaq").keyup(function(){
		var search = $(this)
		var url =$("#qasort").val()+"?q="+$("#qaq").val();
		$("#questionqa .fotorama__spinner").show()
		var curr = $(this)	
		$.ajax({
					url:url,
					success:function(data){				
						$("#qasortquestion").replaceWith($(data).find("#qasortquestion"))
						$("#questionqa .fotorama__spinner").hide()
						var total = $(data).find(".loadmorequestionbutton").data("totalpage")
						
						qa_curr_page = 1;
						
						if( total>1){
							$(".loadmorequestionbutton").data('totalpage',total)
							$(".loadmorequestionbutton").show()
							$(".loadmorequestionbutton").parents(".fieldset").show();
						}
							bindEvents();
					}
				})
	})
	var qa_curr_page = 1;
	
	$("#qasort").on("change",function(){
			$("#questionqa .fotorama__spinner").show()
			var url = $(this).val()
			$.ajax({
				url:url,
				success:function(data){
					
					qa_curr_page =1;
					
					$("#qasortquestion").replaceWith($(data).find("#qasortquestion"))
					
					$("#questionqa .fotorama__spinner").hide()
					$(".loadmorequestionbutton").show()
					$(".loadmorequestionbutton").parents(".fieldset").show()
					bindEvents();
				}
			})
	})
	bindEvents();
})