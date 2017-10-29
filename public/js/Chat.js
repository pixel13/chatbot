/**
 * Manage the chat
 *
 * @param options
 * @require JQuery
 */
function Chat(options)
{
    var defaultOptions = {
        id: '',
        history: $('<div/>'),
        messageInput: $('<input/>'),
        messageSubmit: $('<button/>'),
        waitToTalk: { min : 10, max: 30 },
        pollingInterval: 5000,
        writingTime: { min: 1500, max: 4500 },
        userClass: 'user',
        botClass: 'bot',
        endElement: $('<div/>').addClass('system').text("Chat is terminated")
    };

    var config = {};
    var polling;
    var waitToTalk;

    var checkOptions = function()
    {
        var mandatories = [ "id", "history", "messageInput" ];

        if (typeof options != 'object')
            throw new TypeError("Chat - Invalid argument: a valid JSON object expected as argument");

        for (var i = 0; i < mandatories.length; i++)
        {
            if (!options.hasOwnProperty(mandatories[i]))
                throw  new TypeError("Chat - Invalid argument: missing '" + key + "' in the given argument");
        }

        options = $.extend(defaultOptions, options);
    };

    var readConfiguration = function()
    {
        $.getJSON("config/" + options.id + ".json", function (data) {
           config = data;
        });
    };

    var bindEvents = function()
    {
        options.messageInput.on('keypress', sendEventHandler);
        if (options.hasOwnProperty('messageSubmit'))
            options.messageSubmit.on('click', sendEventHandler);
    };

    var startPolling = function()
    {
        polling = window.setInterval(function() {
            $.ajax({
                method: 'GET',
                url: 'api.php?id=' + options.id,
                dataType: 'json',
                statusCode: {
                    200: function(data)
                    {
                        if (typeof data != 'object')
                            return;

                        $.each(data, function(key, object) {
                            echoMessage(options.botClass, object.message);
                        });
                    },
                    410: function(jqXHR)
                    {
                        publishMessages($.parseJSON(jqXHR.responseText));
                        clearInterval(polling);
                        clearTimeout(waitToTalk);
                        chatEnds();
                    }
                }
            });
        }, options.pollingInterval);
    };

    var chatEnds = function()
    {
        options.history.append(options.endElement);
    };

    var timeString = function(value)
    {
        var result = '';

        if (value < 10)
            result += '0';
        result += value;

        return result;
    };

    var getCurrentTime = function()
    {
        var dt = new Date();
        return timeString(dt.getHours()) + ":" + timeString(dt.getMinutes()) + ":" + timeString(dt.getSeconds());
    };

    var echoMessage = function(sender, message)
    {
        if (message == '')
            return;

        clearTimeout(waitToTalk);

        var text = $('<div/>').text(message);
        var time = $('<div/>').text(getCurrentTime());
        var box = $('<div/>').addClass('message ' + sender).append(text).append(time);

        options.history.append(box);

        talk();
    };

    var sendMessage = function(message)
    {
        echoMessage(options.userClass, message);
        $.ajax({
            method: 'POST',
            url: 'api.php?id=' + options.id,
            contentType: 'application/json; charset=UTF-8',
            data: '{"message": "' + message + '"}',
            dataType: 'json'
        });
    };

    var talk = function()
    {
        if ((options.waitToTalk.min == 0) || (options.waitToTalk.max < options.waitToTalk.min))
            return;

        var seconds = Math.floor((Math.random() * options.waitToTalk.max) + options.waitToTalk.min);

        waitToTalk = setTimeout(function() {
            sendMessage("");
        }, seconds * 1000);
    };

    var sendEventHandler = function(event)
    {
        if ((event.which != 13) && (event.which != 1))
            return;

        var message = options.messageInput.val();
        options.messageInput.val('');
        sendMessage(message);
    };

    checkOptions();
    readConfiguration();
    bindEvents();
    startPolling();
    talk();
}