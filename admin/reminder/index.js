const   nodemailer = require('nodemailer'),
        fs = require('fs'),
        path = require('path'),
        options = require('./options.js'),
        crypto = require('crypto'),
        request = require("sync-request");

const md5 = function (txt) {
    var md5sum = crypto.createHash('md5');
    md5sum.update(txt);
    return md5sum.digest('hex');
}        

var transporter = nodemailer.createTransport(options.nodemailer);
const newsletter='./newsletter.json';

fs.readFile(newsletter,function(err,data){
    var emails=JSON.parse(data);
    var totals=emails.length;
    var embedcache={};
    var sent=0;
    
    const embed = function(url) {
        if (typeof(embedcache[url])!='undefined') return embedcache[url];
        
        var cid=md5(url);
        var pth=__dirname+'/cache/'+cid;
        
        if (!fs.existsSync(pth)){
            var res = request('GET', url);
            fs.writeFileSync(pth,res.getBody());
        }
        
        var filename=path.basename(url);
        if (filename.indexOf('Dwh4MPVAS-QqPbxq9xw2JKv-')>=0) filename='piotr.jpg';
        
        embedcache[url]={
            filename: filename,
            path:pth,
            cid:cid
        };
        
        return embedcache[url];
        
    }
    
    const send = function(r) {
        var to=r.to;
        //to='piotr@reseller.webkameleon.com';
        var html=r.mail;
        var m=html.match(/src="([^"]+)"/g);
        var a=[];
        
        if(m) for (var i=0;i<m.length; i++) {
            var url=m[i].replace('src="','').replace('"','');
            var e=embed(url);
            
            html=html.replace(url,'cid:'+e.cid);
            a.push(e);
        }
        
        transporter.sendMail({
            to: to,
            subject: r.subject,
            from: options.nodemailer.from,
            html: html,
            attachments: a
        }, (error, info) => {
  
            if (error) {
                totals--;
                return console.log('Problem z',to);
                //return console.log(error);
            }

            r.sent=true;
            sent++;
            totals--;
            
            console.log('Message %s sent: %s', info.messageId, info.response);
        });        
    }
    
    
    for (var i=0,j=0; i<emails.length; i++) {
        if (emails[i].sent) {
            totals--;
            continue;
        }
        j++;
        setTimeout(function(ii){
            send(emails[ii]);
        },j*500,i);
        
        
        //break;
    }
    
    
    var endOfAll = false;
    const wait=function() {
        
        fs.writeFile(newsletter,JSON.stringify(emails),function(err){
            
            if (endOfAll) {
                console.log('Wysłano',sent);
                process.exit();
            }
            if (totals>0) {
                console.log('Zostało',totals);
                return setTimeout(wait,1000);
            }
            console.log('Wysłano',sent);
            process.exit();
        });   


    };
    const saveExit = function() {
        endOfAll=true;
    }
    
    wait();
    
    process.on('SIGTERM',saveExit);
    process.on('SIGINT',saveExit);
    
    
});
