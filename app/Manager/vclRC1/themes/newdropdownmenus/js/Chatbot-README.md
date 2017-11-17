VCL Chatbot
========

Summary
----------

The VCL Chatbot answers questions frequently asked by VCL, including questions pertaining to logging in, making reservations, and accessing the Academic Cloud. 

The chatbot running in the VCL webpage was built using the [Watson Conversation service](https://console.bluemix.net/docs/services/conversation/getting-started.html#gettingstarted). We took files from a basic [Node.js app](https://github.com/watson-developer-cloud/conversation-simple) that sends  messages to and receives responses from the Watson Conversation service running in Bluemix, and we deposited them into our code for the VCL webpage.

Importing and Editing the Conversation
--------

1. Go to your [Bluemix account](https://console.bluemix.net/). Go to the Catalog and create an instance of the Watson Conversation service. (The Lite pricing plan will suffice.)
2. In your Watson Conversation instance, click 'Launch Tool'. 
3. Click the import icon next to the 'Create' button to import a workspace. 
4. Find the JSON file for the VCL Chatbot workspace and import it into your instance of Watson Conversation. [Here](https://www.dropbox.com/s/fbh6up6bofdi20i/vclChatbot.json?dl=0) is the current version of the chatbot workspace.
5. Edit the conversation. You can change the intents, add more training examples, restrucure the dialog flow, etc. [Here](https://console.bluemix.net/docs/services/conversation/getting-started.html#gettingstarted) is the documentation for Watson Conversation.

Calling your Conversation Service
-------------------------
[Here](https://www.ibm.com/watson/developercloud/conversation/api/v1/#send_message) is the API for the Watson Conversation service, which gives you instructions on how to send and receive messages from the service. 

In order to communicate with your Conversation service, you need three ID's: `CONVERSATION_USERNAME`, `CONVERSATION_PASSWORD`, `WORKSPACE_ID`. You can find the first two ID's under the 'Service credentials' tab of your Watson Conversation instance:

![here](http://i.imgur.com/WbCWCgf.png)

The `WORKSPACE_ID` can be found when you launch the Conversation service. If you select 'View Details' under a specific workspace, you can find the `WORKSPACE_ID`:

<img src="http://i.imgur.com/5YPlhfh.png" width="500">

*Note: the VCL website that is currently up and running is communicating with the chatbot I made. If you want to redirect it to talk to your chatbot, you will need to change these three ID's to the ones associated with your Conversation service.


Integrating the Chatbot into VCL Website
------------

If you go to `vcl-theme/themes/newdropdownmenus/js` in the VCL website repository, you will find the files associated with the Chatbot:

* `.env`: contains the ID's for the Conversation service you want your app to communicate with
* `api.js`: you will need to change the `messageEndpoint` to the URL of your Conversation service. This should be found under the 'Service credentials' tab
* `chatbot.js`: contains jQuery for the front-end of the chatbot
* `conversation.js`: vestigial front-end stuff. You probably don't have to worry about this

If for some reason you want to completely overhaul the way that the Chatbot in VCL communicates with the Conversation service you create, you may want to look at [this sample](https://github.com/watson-developer-cloud/conversation-simple) as a reference.

License
-------
Apache 2.0
