var server_link = "https://zadmin.colomboshabab.com/";

$(document).ready(function () {
    if($('.datatable-1').length>0){
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass('btn-group datatable-pagination');
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');

        $( '.slider-range').slider({
			    range: true,
			    min: 0,
			    max: 20000,
			    values: [ 3000, 12000 ],
			    slide: function(event, ui) {
				    $(this).find('.ui-slider-handle').attr('title', ui.value);
			    },
	    });
        
        $( '#amount' ).val( '$' + $( '.slider-range' ).slider( 'values', 0 ) + ' - $' + $( '.slider-range' ).slider( 'values', 1 ) );


    //Graph/Chart index.html

    var d1 = [ [0, 1], [1, 14], [2, 5], [3, 4], [4, 5], [5, 1], [6, 14], [7, 5],  [8, 5] ];
    var d2 = [ [0, 5], [1, 2], [2, 10], [3, 1], [4, 9],  [5, 5], [6, 2], [7, 10], [8, 8] ];

		var plot = $.plot($('#placeholder2'),
			   [ { data: d1, label: 'Profits'}, { data: d2, label: 'Expenses' } ], {
					lines: {
						show: true,
						fill: true, /*SWITCHED*/
						lineWidth: 2
					},
					points: {
						show: true,
						lineWidth: 5
					},
					grid: {
						clickable: true,
						hoverable: true,
						autoHighlight: true,
						mouseActiveRadius: 10,
						aboveData: true,
						backgroundColor: '#fff',
						borderWidth: 0,
						minBorderMargin: 25,
					},
					colors: [ '#55f3c0', '#0db37e',  '#b4fae3', '#e0d1cb'],
					shadowSize: 0
				 });

		function showTooltip(x, y, contents) {
			$('<div id="gridtip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5
			}).appendTo('body').fadeIn(300);
		}

		var previousPoint = null;
		$('#placeholder2').bind('plothover', function (event, pos, item) {
			$('#x').text(pos.x.toFixed(2));
			$('#y').text(pos.y.toFixed(2));

			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;

					$('#gridtip').remove();
					var x = item.datapoint[0].toFixed(0),
						y = item.datapoint[1].toFixed(0);

					showTooltip(item.pageX, item.pageY,
								'x : ' + x + '&nbsp;&nbsp;&nbsp; y : ' + y);
				}
			}
			else {
				$('#gridtip').remove();
				previousPoint = null;
			}
		});
    }

    else
    {
        var d1 = [ [0, 1], [1, 14], [2, 5], [3, 4], [4, 5], [5, 1], [6, 14], [7, 5],  [8, 5] ];
		var d2 = [ [0, 5], [1, 2], [2, 10], [3, 1], [4, 9],  [5, 5], [6, 2], [7, 10], [8, 8] ];

		var plot = $.plot($("#placeholder"),
		[ { data: d1, label: "Data A" }, { data: d2, label: "Data B" } ], {
			lines: {
				show: true,
				fill: false,
				lineWidth: 2
			},
			points: {
				show: true,
				lineWidth: 5
			},
			grid: {
				clickable: true,
				hoverable: true,
				autoHighlight: true,
				mouseActiveRadius: 10,
				aboveData: true,
				backgroundColor: "#fafafa",
				borderWidth: 0,
				minBorderMargin: 25,
			},
			colors: [ "#090", "#099",  "#609", "#900"],
			shadowSize: 0
		});

        var d1 = [ [0, 1], [1, 14], [2, 5], [3, 4], [4, 5], [5, 1], [6, 14], [7, 5],  [8, 5] ];
		var d2 = [ [0, 5], [1, 2], [2, 10], [3, 1], [4, 9],  [5, 5], [6, 2], [7, 10], [8, 8] ];

		var plot = $.plot($("#placeholder2"),
			   [ { data: d1, label: "Data Y"}, { data: d2, label: "Data X" } ], {
					lines: {
						show: true,
						fill: true, /*SWITCHED*/
						lineWidth: 2
					},
					points: {
						show: true,
						lineWidth: 5
					},
					grid: {
						clickable: true,
						hoverable: true,
						autoHighlight: true,
						mouseActiveRadius: 10,
						aboveData: true,
						backgroundColor: "#fafafa",
						borderWidth: 0,
						minBorderMargin: 25,
					},
					colors: [ "#090", "#099",  "#609", "#900"],
					shadowSize: 0
				 });

		function showTooltip(x, y, contents) {
			$('<div id="gridtip">' + contents + '</div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5
			}).appendTo("body").fadeIn(300);
		}

		var previousPoint = null;
		$("#placeholder2").bind("plothover", function (event, pos, item) {
			$("#x").text(pos.x.toFixed(2));
			$("#y").text(pos.y.toFixed(2));

			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;

					$("#gridtip").remove();
					var x = item.datapoint[0].toFixed(0),
						y = item.datapoint[1].toFixed(0);

					showTooltip(item.pageX, item.pageY,
								"x : " + x + "&nbsp;&nbsp;&nbsp; y : " + y);
				}
			}
			else {
				$("#gridtip").remove();
				previousPoint = null;
			}
		});

          // PREDEFINED DATA
        var data = [
						{ label: "Series1", data: [[1, 10]] },
						{ label: "Series2", data: [[1, 30]] },
						{ label: "Series3", data: [[1, 90]] }
					];

        // DEFAULT
        $.plot($("#pie-default"), data,
		{
		    series: {
		        pie: {
		            show: true
		        }
		    }
		});

        // DEFINE ACTIONS FOR pieHover & pieClick
        function pieHover(event, pos, obj) {
            if (!obj)
                return;
            percent = parseFloat(obj.series.percent).toFixed(2);
            $("#hover").html('<span>' + obj.series.label + ' - ' + percent + '%</span>');
        }

        function pieClick(event, pos, obj) {
            if (!obj)
                return;
            percent = parseFloat(obj.series.percent).toFixed(2);
            alert('' + obj.series.label + ': ' + percent + '%');
        }

        // DONUT
        $.plot($("#pie-donut"), data,
		{
		    series: {
		        pie: {
		            innerRadius: 50,
		            show: true
		        }
		    }
		});

        // DONUT + INTERACTIVE
        $.plot($("#pie-interactive"), data,
		{
		    series: {
		        pie: {
		            innerRadius: 50,
		            show: true
		        }
		    },
		    grid: {
		        hoverable: true,
		        clickable: true
		    }
		});

        $("#pie-interactive").bind("plothover", pieHover);
        $("#pie-interactive").bind("plotclick", pieClick);
	}

	$('.datepicker').datepicker({
		todayHighlight: true
	});

	$('[data-toggle="tooltip"]').tooltip();
    
});

$('#preits').change(function () {
	if ($('#preits').val() == '' || $('#preits').val().length < 8) {
		return false;
	}
	$(".invisi").css("display","block");
	//check if this its already exists in the system
	$.ajax({
		url: server_link + "check_its_exists.php?itsid=" + $("#preits").val() + "&safarid=" + $("#safarid").val(),
		success: function (data) {
			if (data == "1") { //we can go ahead and display the details
				var url = server_link + "member-autofill.php?its=" + $('#preits').val();
				//$('#process').show();
				//alert(url);
				$.getJSON(url, function (json) {
					// loop through the members here
					//alert(url);
					$.each(json.details, function (i, dat) {
						$("input[name='itsname']").val(dat.name);
						$("input[name='contact']").val(dat.personalMobileNo);
						$("input[name='email']").val(dat.permenentEmail);
						//try jpg
						$.get("https://colombojamaat.org/files/ejamaat_photos/" + $("#preits").val() + ".jpg")
							.done(function() {
								$("#profileImg").attr("src", "https://colombojamaat.org/files/ejamaat_photos/" + $("#preits").val() + ".jpg");
							}).fail(function() {
								$("#profileImg").attr("src", server_link + "images/no_image.jpg");
								//try JPG
								$.get("https://colombojamaat.org/files/ejamaat_photos/" + $("#preits").val() + ".JPG")
								.done(function() {
									$("#profileImg").attr("src", "https://colombojamaat.org/files/ejamaat_photos/" + $("#preits").val() + ".JPG");
								}).fail(function() {
									$("#profileImg").attr("src", server_link + "images/no_image.jpg");
								});
							});
					});
				});
				$("#itsno_prereg_help").html("Eight digit number");
			} else {
				alert("This ITS is already registered for this Safar. OR your ITS may not exist in our database. You may continue to update the details manually.");
				$("input[name='itsname']").val("");
				$("input[name='contact']").val("");
				$("input[name='email']").val("");
				$("#profileImg").attr("src", server_link + "images/no_image.jpg");
				$("#itsno_prereg_help").html("This ITS is already registered for this Safar. OR your ITS may not exist in our database. You may continue to update the details manually.");
			}
			$(".invisi").css("display","none");
		}
	});
});

$('.kun-status').on('change', function() {
    console.log("activated");
    var status_form = $(this).closest('form');

    url_string = status_form.attr('action');

    $.ajax({
       type: "POST",
       url: url_string,
       data: status_form.serialize(), // serializes the form's elements.
       error: function(jqXHR) { 
            if(jqXHR.status==0) {
                alert("Fail to connect, please check your connection settings");
            }
        },
        success: function(data)
        {
           if(data != 1){
               //something gone wrong
               alert("Oops, something went wrong. Pls try again.");
           }
        }
     });
});


$('#sortable').sortable({
    axis: 'y',
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
		var safarid = $("#safarid").html();

		console.log(data);

        //POST to server using $.post or $.ajax
        $.ajax({
            data: data,
            type: 'POST',
            url: "../../../update_grouping.php?safarid=" + safarid,
        });
    }
});

$('.group-manifest').change(function() {
	//get the kunid
	// var kunid = $(this).data("kunid");
	var kunid = $(this).attr('id').replace('manifest-kun-','');
	var manifestgroup = $(this).val();
	$.ajax({
		type: 'POST',
		url: "../../../update_groupmanifest.php?kunid=" + kunid + "&manifestgroup=" + manifestgroup,
	});
});

$('.group-mowafeka').change(function() {
	//get the kunid
	// var kunid = $(this).data("kunid");
	var kunid = $(this).attr('id').replace('mowafeka-kun-','');
	var mowafeka = $(this).val();
	$.ajax({
		type: 'POST',
		url: "../../../update_mowafeka.php?kunid=" + kunid + "&mowafeka=" + mowafeka,
	});
});

// Initialize Ekko Lightbox for images
$(document).ready(function() {
	console.log('Initializing Ekko Lightbox...');
	
	// Wait a bit for all content to load
	setTimeout(function() {
		// Check if ekkoLightbox is available
		if (typeof $.fn.ekkoLightbox === 'undefined') {
			console.error('Ekko Lightbox not loaded!');
			return;
		}
		
		console.log('Ekko Lightbox is available, initializing...');
		
		// Initialize all existing lightbox links
		$('[data-toggle="lightbox"]').each(function() {
			console.log('Found lightbox link:', this.href);
		});
		
		// Set up click handler for lightbox links
		$(document).on('click', '[data-toggle="lightbox"]', function(event) {
			console.log('Lightbox clicked:', this.href);
			event.preventDefault();
			
			// Initialize lightbox for this specific link
			$(this).ekkoLightbox({
				alwaysShowClose: true,
				showArrows: true,
				wrapping: true
			});
		});
		
		// Also initialize all existing links
		$('[data-toggle="lightbox"]').ekkoLightbox({
			alwaysShowClose: true,
			showArrows: true,
			wrapping: true
		});
		
	}, 500);
});

// PDF Modal functionality
function openPdfModal(pdfUrl) {
	// Set the PDF URL in the iframe
	document.getElementById('pdfViewer').src = pdfUrl;
	// Set the download link
	document.getElementById('pdfDownload').href = pdfUrl;
	// Show the modal
	$('#pdfModal').modal('show');
}