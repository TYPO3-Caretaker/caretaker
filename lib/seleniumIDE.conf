/**
* Parse source and update TestCase. Throw an exception if any error occurs.
*
* @param testCase TestCase to update
* @param source The source to parse
*/
function parse(testCase, source) {
   var commands = [];
   lines = source.split('\n');
   for(i = 0;i<lines.length;i++) {

       firstChar = lines[i].replace(/ /,'').substr(0,1);
       if(firstChar != '-' && firstChar != '#' && firstChar != ':') {

           commandText = lines[i].substr(0,lines[i].search(/:/));
           //commentText = lines[i].substr(search(/:/));

           var command = new Command();
           command.command = commandText
           paramCount = 0;

           for(additionalLines = i+1;additionalLines<lines.length;additionalLines++) {

               nextLineFirstChar = lines[additionalLines].replace(/ /,'').substr(0,1);

               if(nextLineFirstChar != '-' && nextLineFirstChar != ':' && nextLineFirstChar != '#') {
                   i = additionalLines - 1;
                   break;
               }

               if(nextLineFirstChar == '-') {
                   if(paramCount == 1) {
                       paramCount++;
                       command.value = lines[additionalLines].substr(1).replace(/ /,'');
                   }
                   if(paramCount == 0) {
                       paramCount++;
                       command.target = lines[additionalLines].substr(1).replace(/ /,'');
                   }
               }
           }
           commands.push(command);
       }
   }
   testCase.setCommands(commands);
}

/**
* Format TestCase and return the source.
*
* @param testCase TestCase to format
* @param name The name of the test case, if any. It may be used to embed title into the source.
*/
function format(testCase, name) {
 return formatCommands(testCase.commands);
}

/**
* Format an array of commands to the snippet of source.
* Used to copy the source into the clipboard.
*
* @param The array of commands to sort.
*/
function formatCommands(commands) {
   var result = '';
   var commands = commands;
   for (var i = 0; i < commands.length; i++) {
       var command = commands[i];
       if (command.type == 'command' && command.command != '') {
           result += command.command + ':\n';
           if(command.target != '') result += '-' + command.target + '\n';
           if(command.value != '') result += '-' + command.value + "\n";
       }
   }
   return result;
}

/*
* Optional: The customizable option that can be used in format/parse functions.
*/
//options = {nameOfTheOption: 'The Default Value'}

/*
* Optional: XUL XML String for the UI of the options dialog
*/
//configForm = '<textbox id="options_nameOfTheOption"/>'


