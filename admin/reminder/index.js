const   nodemailer = require('nodemailer')
        fs = require('fs'),
        options = require('./options.js');
        

var transporter = nodemailer.createTransport(options.nodemailer);
const newsletter='./newsletter.json';

fs.readFile(newsletter,function(err,data){
    var emails=JSON.parse(data);
    var totals=emails.length;
    
    
    const send = function(r) {
        transporter.sendMail({
            to: r.to,
            subject: r.subject,
            from: options.nodemailer.from,
            html: r.mail
        }, (error, info) => {
  
            if (error) {
                totals--;
                return console.log('Problem z',r.to);
                //return console.log(error);
            }
            r.sent=true;
            totals--;
            
            console.log('Message %s sent: %s', info.messageId, info.response);
        });        
    }
    
    
    for (var i=0; i<emails.length; i++) {
        if (emails[i].sent) {
            totals--;
            continue;
        }
        send(emails[i]);
        
        //break;
    }
    
    const wait=function() {
        
        fs.writeFile(newsletter,JSON.stringify(emails),function(err){
            if (totals>0) {
                console.log('Zostało',totals);
                return setTimeout(wait,1000);
            }
            process.exit();
        });   


    };
    
    wait();
    
    
});
