const developersList = [];
const collectionsList = [];

document.addEventListener('DOMContentLoaded', function() {
  let addDeveloperForm = document.getElementById('form-add-developer');
  addDeveloperForm.addEventListener('submit', onAddDeveloperFormSubmit);
  let addCollectionForm = document.getElementById('form-add-collection');
  addCollectionForm.addEventListener('submit', onAddCollectionFormSubmit);
  let btnCopyData = document.getElementById('btn-copy-data');
  btnCopyData.addEventListener('click', onCopyDataSubmit);
});

const onAddDeveloperFormSubmit = function (e) {
  // prevent form submit
  e.preventDefault();

  // get developer name
  let devName = document.getElementById('input-developer').value;


  // generate slug of developer
  let today = new Date();
  devName += '-' + (today.getMonth() + 1);
  devName += '-' + (today.getDate());
  
  // add developer to list
  developersList.push(devName);

  // generate html
  listHtml = generateDevListHTML(developersList);
  document.getElementById('list-developers').innerHTML = listHtml;
  
  // empty input for new one
  document.getElementById('input-developer').value = '';
};

const onAddCollectionFormSubmit = function (e) {
  // prevent form submit
  e.preventDefault();

  // get developer name
  let collectionName = document.getElementById('input-collection').value;

  collectionsList.push(collectionName);

  // generate html
  listHtml = generateDevListHTML(collectionsList);
  document.getElementById('list-collections').innerHTML = listHtml;
  
  // empty input for new one
  document.getElementById('input-collection').value = '';
};

const generateDevListHTML = function(items) {
  html = "";
  for (const item of items) {
    html += `<li class="list-group-item">${item}</li>`;
  }
  return html;
};

const onCopyDataSubmit = function() {
  const targetUrl = document.getElementById('targetConnectionUrl').value;
  const destinationUrl = document.getElementById('destinationConnectionUrl').value;
  let errorMsg = "";
  if (!targetUrl) {
    errorMsg += "Please enter target Monogdb Url.<br/>";
  }
  if (!destinationUrl) {
    errorMsg += "Please enter destination Monogdb Url.<br/>";
  }
  if (developersList.length === 0) {
    errorMsg += "Please add developers' names.";
  }
  if (collectionsList.length === 0) {
    errorMsg += "Please add collections' names.";
  }

  const errorMsgDiv = document.getElementById('error-msg');
  const successMsgDiv = document.getElementById('success-msg');
  if (errorMsg) {
    // if there are errors, show error message
    errorMsgDiv.innerHTML = errorMsg;
    errorMsgDiv.classList.remove('d-none');
    successMsgDiv.classList.add('d-none');
  } else {
    errorMsgDiv.classList.add('d-none');
    const token = document.head.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "/connect", true); 
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("X-CSRF-TOKEN", token);
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        // Response
        var response = this.responseText;
        successMsgDiv.innerHTML = response;
        successMsgDiv.classList.remove('d-none');
        errorMsgDiv.classList.add('d-none');
      } else {
        errorMsgDiv.innerHTML = "Connection Failed Or Something went wrong.";
        errorMsgDiv.classList.remove('d-none');
        successMsgDiv.classList.add('d-none');
      }
    };
    var data = {
      targetConnectionUrl: targetUrl,
      destinationConnectionUrl: destinationUrl,
      developers: developersList,
      collections: collectionsList,
    };
    xhttp.send(JSON.stringify(data));
  }
  

};