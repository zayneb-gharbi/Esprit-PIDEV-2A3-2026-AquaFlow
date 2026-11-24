import {PHP, loadPHPRuntime} from '@php-wasm/universal';
import {getPHPLoaderModule} from '@php-wasm/node-8-4';
import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
import {generateKeyPairSync} from 'node:crypto';
export const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'../../..');
export const php=new PHP(await loadPHPRuntime(await getPHPLoaderModule()));
php.mkdir('/app');
function copy(src,dst){
  for(const x of fs.readdirSync(src,{withFileTypes:true})){
    if(['.git','node_modules','local.php'].includes(x.name)||x.name.endsWith('.zip'))continue;
    if(src.endsWith('/amir/var')&&!['.gitkeep','.htaccess'].includes(x.name))continue;
    const a=path.join(src,x.name),b=dst+'/'+x.name;
    if(x.isDirectory()){php.mkdir(b);copy(a,b)}else php.writeFile(b,fs.readFileSync(a));
  }
}
copy(root,'/app');
php.writeFile('/app/aquaflow_demo/amir/var/cle.txt','ab'.repeat(32));
php.writeFile('/app/aquaflow_demo/amir/config/local.php',`<?php return ['google_id'=>'test-google-client','google_secret'=>'test-google-secret','facebook_id'=>'test-facebook-app','facebook_secret'=>'test-facebook-secret'];`);
const keys=generateKeyPairSync('rsa',{modulusLength:2048,publicKeyEncoding:{type:'spki',format:'pem'},privateKeyEncoding:{type:'pkcs8',format:'pem'}});
php.writeFile('/app/aquaflow_demo/amir/tests/private-test.pem',keys.privateKey);
php.writeFile('/app/aquaflow_demo/amir/tests/public-test.pem',keys.publicKey);
const syntax=await php.run({code:`<?php $n=0;foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator('/app')) as $f){if($f->getExtension()==='php'){token_get_all(file_get_contents($f->getPathname()), TOKEN_PARSE);$n++;}}echo $n." fichiers PHP : syntaxe OK (PHP ".PHP_VERSION.")\\n";`});
console.log(syntax.text);
if(syntax.exitCode||syntax.errors)throw Error(syntax.errors+syntax.text);
export const base='/app/aquaflow_demo/amir';
export const results=[];
export function assert(ok,label){if(!ok)throw Error(label);results.push({test:label,status:'OK'});}
const unit=await php.run({scriptPath:base+'/tests/verifier.php',env:{AQUAFLOW_TEST_DB:base+'/var/unit.sqlite'}});
console.log(unit.text);
if(unit.exitCode||unit.errors)throw Error(unit.errors+unit.text);
