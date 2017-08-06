const   nodemailer = require('nodemailer')
        fs = require('fs'),
        options = require('./options.js');
        

var transporter = nodemailer.createTransport(options.nodemailer);
const newsletter='./newsletter.json';

fs.readFile(newsletter,function(err,data){
    var emails=JSON.parse(data);
    var totals=emails.length;
    
    totals=1;
    
    const send = function(r) {
        transporter.sendMail({
            to: 'piotr@reseller.webkameleon.com',
            subject: r.subject,
            from: options.nodemailer.from,
            html: r.mail
        }, (error, info) => {
            if (error) {
                return console.log(error);
            }
            r.sent=true;
            totals--;
            
            console.log('Message %s sent: %s', info.messageId, info.response);
        });        
    }
    
    
    for (var i=0; i<emails.length; i++) {
        if (emails[i].sent) continue;
        send(emails[i]);
        
        break;
    }
    
    const wait=function() {
        
        if (totals>0) return setTimeout(wait,500);
    
        fs.writeFile(newsletter,JSON.stringify(emails),function(err){
            console.log('Zapisane');
            process.exit(); 
        });
    };
    
    wait();
    
    
});
