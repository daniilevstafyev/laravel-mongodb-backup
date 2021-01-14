@extends('layouts.default')

@section('content')
<div class="container">
  <div class="row">
    <input type="hidden" id="batch_id" value="{{$batchId}}">
    <div id="info-text">Duplicating databases. Plesae wait and don't reload page ...</div>
    <div class="progress">
      <div id="progress-text" class="progress-done" data-done="{{$progress}}">
        {{$progress}}%
      </div>
    </div>
  </div>
  <a href="/" id="btn-homepage" class="btn btn-primary">New Duplicate</a>
</div>
<script>
  const btnHomepage = document.getElementById('btn-homepage');
  btnHomepage.style.visibility = 'hidden';
  const progress = document.querySelector('.progress-done');
  const progressText = document.getElementById('progress-text');
  if (progress) {
    progress.style.width = progress.getAttribute('data-done') + '%';
    progress.style.opacity = 1;
  }
  var timer;

  const getProgressStatus = function () {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var batchObj = JSON.parse(this.responseText);
        var completedPercent = batchObj.progress;
        progress.style.width = completedPercent + '%';
        progressText.innerHTML = completedPercent + '%';
        if (completedPercent === 100) {
          clearInterval(timer);
          btnHomepage.style.visibility = 'visible';
          const infoText = document.getElementById('info-text');
          infoText.innerHTML = 'Completed!';
        }
      }
    };
    xhttp.open("GET", "/batch?id={{$batchId}}", true); 
    xhttp.send();
  }

  timer = setInterval(getProgressStatus, 2000);
</script>
@endsection