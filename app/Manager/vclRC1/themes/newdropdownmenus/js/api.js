// The Api module is designed to handle all interactions with the server

var Api = (function() {
  var requestPayload;
  var responsePayload;
  var messageEndpoint = 'https://conversation-vcl.mybluemix.net/api/message';

  // Publicly accessible methods defined
  return {
    sendRequest: sendRequest,

    // The request/response getters/setters are defined here to prevent internal methods
    // from calling the methods without any of the callbacks that are added elsewhere.
    getRequestPayload: function() {
      return requestPayload;
    },
    setRequestPayload: function(newPayloadStr) {
      requestPayload = JSON.parse(newPayloadStr);
    },
    getResponsePayload: function() {
      return responsePayload;
    },
    setResponsePayload: function(newPayloadStr) {
      responsePayload = JSON.parse(newPayloadStr);
    }
  };

  // Send a message request to the server
  function sendRequest(text, context) {

    // Build request payload
    var payloadToWatson = {};
    if (text) {
      payloadToWatson.input = {
        text: text
      };
    }
  //  context = {conversation_id: "TEST"};
    if (context) {
      payloadToWatson.context = context;
    }

    // Built http request

    var http = new XMLHttpRequest({mozSystem: true});
    http.open('POST', messageEndpoint, true);
    http.setRequestHeader('Content-type', 'application/json');
    http.onreadystatechange = function() {
      console.log(http);
       if (http.readyState === 4 && http.status === 200 && http.responseText) {
        Api.setResponsePayload(http.responseText);
      }
    };


console.log(payloadToWatson);
    var params = JSON.stringify(payloadToWatson);
    // Stored in variable (publicly visible through Api.getRequestPayload)
    // to be used throughout the application

    if (Object.getOwnPropertyNames(payloadToWatson).length !== 0) {
      Api.setRequestPayload(params);
    }

console.log(params);
    // Send request
    http.send(params);
    /*
    $.post(messageEndpoint,
      params,
      function(data, status) {
        console.log("data: " + data + " Status: " + status);
      });
      */
/*
    $.ajax({
      type: "POST",
      url: messageEndpoint,
      dataType: 'json',
      data: params,
      success: function(data){
        console.log(data);
      },
      error: function(jqXHR, data){
        console.log(jqXHR);
        console.log(data);
      }
    })
*/
  }
}());
