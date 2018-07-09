<!DOCTYPE html>
<html>
<head>
	<title></title>
<style type="text/css">
		
.table td {
	width: 33%;
	border: 3px dashed #ccc;
	height: 250px;
}
.table td.hover { border: 3px dashed #0c0; }
</style>
</head>
<body>
<table class="table" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
		</tr>
		<tr>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
		</tr>
		<tr>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
			<td class="box" ondragover="onDragHandler(event);" ondragleave="onDragEndHandler(event);" ondrop="dropHandler(event);"></td>
		</tr>
	</tbody>
</table>
</body>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
var tests = {
      filereader: typeof FileReader != 'undefined',
      dnd: 'draggable' in document.createElement('span'),
      formdata: !!window.FormData,
      progress: "upload" in new XMLHttpRequest
    },
    acceptedTypes = {
      'image/png': true,
      'image/jpeg': true,
      'image/gif': true
    };



function onDragHandler(e) {
	e.preventDefault();
	var td = $(e.target);

	if (td.is('img'))
		td = td.parent();
		
	//console.log(td.is('img'));
	td.addClass('hover');
	//this.className = 'hover'; 
	//console.log('dragover');
	return false;
}
function onDragEndHandler(e) {
	e.preventDefault();
	var td = $(e.target);
	if (td.is('img'))
		td = td.parent();
	if (td.hasClass('hover'))
		td.removeClass('hover');
	//console.log('dragend');
	return false;
}
function dropHandler(ev) {
  console.log('File(s) dropped');
  var td = $(ev.target);
  if (td.is('img'))
		td = td.parent();
  td.children('img').remove();
  //console.log(td[0].clientWidth);

  // Prevent default behavior (Prevent file from being opened)
  ev.preventDefault();
  //console.log(ev.dataTransfer.files);
  readfiles(ev.dataTransfer.files, td);

  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.items.length; i++) {
      // If dropped items aren't files, reject them
      if (ev.dataTransfer.items[i].kind === 'file') {
        var file = ev.dataTransfer.items[i].getAsFile();
        //console.log('... file[' + i + '].name = ' + file.name);
      }
    }
  } else {
    // Use DataTransfer interface to access the file(s)
    for (var i = 0; i < ev.dataTransfer.files.length; i++) {
      //console.log('... file[' + i + '].name = ' + ev.dataTransfer.files[i].name);
    }
  } 
  
  // Pass event to removeDragData for cleanup
  if (td.hasClass('hover'))
		td.removeClass('hover');
	//console.log('dragend');
  removeDragData(ev)
}
function removeDragData(ev) {
  //console.log('Removing drag data')

  if (ev.dataTransfer.items) {
    // Use DataTransferItemList interface to remove the drag data
    ev.dataTransfer.items.clear();
  } else {
    // Use DataTransfer interface to remove the drag data
    ev.dataTransfer.clearData();
	}
}


function previewfile(file, el) {
  if (tests.filereader === true && acceptedTypes[file.type] === true) {
    var reader = new FileReader();
    reader.onload = function (event) {
      var image = new Image();
      image.src = event.target.result;
      image.width = el[0].clientWidth; // a fake resize
      image.height = el[0].clientHeight; // a fake resize
      el.append(image);
      //holder.appendChild(image);
      //console.log(image);
    };

    reader.readAsDataURL(file);
  }  else {
    holder.innerHTML += '<p>Uploaded ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '');
    //console.log(file);
  }
}

function readfiles(files, el) {
    //debugger;
    var formData = tests.formdata ? new FormData() : null;
    for (var i = 0; i < files.length; i++) {
      if (tests.formdata) formData.append('file', files[i]);
      previewfile(files[i], el);
    }
    /*
    // now post a new XHR request
    if (tests.formdata) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', '/devnull.php');
      xhr.onload = function() {
        progress.value = progress.innerHTML = 100;
      };

      if (tests.progress) {
        xhr.upload.onprogress = function (event) {
          if (event.lengthComputable) {
            var complete = (event.loaded / event.total * 100 | 0);
            progress.value = progress.innerHTML = complete;
          }
        }
      }

      xhr.send(formData);
      */
}



$(document).ready(function(e){
  $('.box').on('dblclick', function(e){
    e.preventDefault();
    $(this).html('');
  });

})
</script>
</html>