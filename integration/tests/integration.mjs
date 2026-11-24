import {PHP,loadPHPRuntime} from '@php-wasm/universal';
import {getPHPLoaderModule} from '@php-wasm/node-8-4';
import fs from 'node:fs';
import {generateKeyPairSync} from 'node:crypto';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
const php=new PHP(await loadPHPRuntime(await getPHPLoaderModule()));
php.mkdir('/app');
function copy(src,dst){for(const x of fs.readdirSync(src,{withFileTypes:true})){if(x.name.startsWith('.')||['node_modules','vendor','local.php','import.local.php','storage','var'].includes(x.name))continue;const a=path.join(src,x.name),b=dst+'/'+x.name;if(x.isDirectory()){php.mkdir(b);copy(a,b)}else if(!x.isSymbolicLink())php.writeFile(b,fs.readFileSync(a));}}
copy(root,'/app');if(process.env.AQUAFLOW_TEST_XLSX){php.mkdir('/app/vendor');copy(root+'/vendor','/app/vendor');}php.mkdir('/app/amir/var');
php.writeFile('/app/amir/var/cle.txt','ab'.repeat(32));
php.writeFile('/app/amir/config/local.php',"<?php return ['google_id'=>'test-client','google_secret'=>'test-secret','facebook_id'=>'test-facebook','facebook_secret'=>'test-secret'];");
const env={AQUAFLOW_TEST_DB:'/app/amir/var/test.sqlite'};
async function run(code){const r=await php.run({env,code:'<?php '+code});if(r.errors||r.exitCode)throw Error(r.errors+r.text);return r.text;}
console.log(await run("$n=0;foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator('/app')) as $f){if($f->getExtension()==='php'){token_get_all(file_get_contents($f->getPathname()),TOKEN_PARSE);$n++;}}echo $n.' fichiers PHP valides';"));
const group=fs.readFileSync(root+'/mode.php','utf8').includes("'groupe'");
await run("require '/app/amir/config/bootstrap.php';require '/app/amir/models/Installation.php';$db=AmirDatabase::connexion();Installation::schema($db);Installation::schema($db);$m=new UtilisateurPersistant($db);foreach([[3,'Admin','admin@test.test'],[1,'Citoyen','citoyen@test.test'],[2,'Plombier','plombier@test.test']] as [$r,$n,$e]){$m->create($n,$e,'TestMotDePasse42!',$r);}");
const results=[],jars={};
function ok(value,label){if(!value)throw Error(label);results.push(label);}
async function req(client,query,post){const headers={host:'localhost',...(jars[client]?{cookie:jars[client]}:{})};if(post)headers['content-type']='application/x-www-form-urlencoded';const r=await php.run({scriptPath:'/app/amir/public/index.php',relativeUri:'/amir/public/index.php?'+query,method:post?'POST':'GET',body:post?new TextEncoder().encode(new URLSearchParams(post).toString()):undefined,headers,env,$_SERVER:{REMOTE_ADDR:client}});for(const c of r.headers['set-cookie']||[])if(c.startsWith('AQUAFLOW_INTEGRATION_'))jars[client]=c.split(';')[0];if(r.errors)throw Error(r.errors+'\n'+query+'\n'+r.text.slice(0,300));return r;}
function csrf(r){const m=r.text.match(/name="csrf(?:_token)?" value="([^"]+)"/);if(!m)throw Error('csrf absent '+r.text.slice(0,500));return m[1];}
function loc(r){return (r.headers.location||[])[0]||'';}
async function login(c,e,p='TestMotDePasse42!'){let r=await req(c,'page=login');const old=jars[c];r=await req(c,'page=login',{csrf_token:csrf(r),email:e,password:p});ok(r.httpStatusCode===303,'Connexion '+c+' ('+r.httpStatusCode+')');ok(old!==jars[c],'Session renouvelée '+c);return r;}
let r=await req('guest','page=login');ok(r.text.includes('Google')&&r.text.includes('Facebook'),'Deux fournisseurs visibles');ok(r.headers['cache-control'].includes('no-store'),'Pages sensibles sans cache');
r=await req('guest','page=login',{});ok(r.httpStatusCode===403,'CSRF obligatoire');
r=await req('guest','page=details&id=2');ok(loc(r).includes('login'),'Fiche interdite aux visiteurs');
await login('admin','admin@test.test');await login('citoyen','citoyen@test.test');await login('plombier','plombier@test.test');
for(const c of ['citoyen','plombier'])for(const p of ['admin','details&id=1','users']){r=await req(c,'page='+p);ok(r.httpStatusCode===403,c+' sans droits admin : '+p);}
for(const p of ['profile','security','change_password','admin','details&id=2']){r=await req('admin','page='+p);ok(r.httpStatusCode===200,'Page admin '+p);}
r=await req('admin','page=details&id=2');ok(!r.text.includes('$2y$')&&!r.text.includes('totp_secret')&&r.text.includes('citoyen@test.test'),'Fiche sans données secrètes');
r=await req('admin','page=details&id[]=2');ok(r.httpStatusCode===400,'Identifiant invalide refusé');
r=await req('citoyen','page=security');r=await req('citoyen','page=security',{csrf:csrf(r),action:'prepare'});ok(r.httpStatusCode===303,'Préparation TOTP');
r=await req('citoyen','page=security');const secret=r.text.match(/class="secret">([^<]+)</)[1];const code=await run("require '/app/amir/controllers/TotpController.php';echo AmirTotp::code('"+secret+"',intdiv(time(),30));");
r=await req('citoyen','page=security',{csrf:csrf(r),action:'enable',code});ok(r.httpStatusCode===303,'Activation TOTP');
r=await req('citoyen','page=security');const recovery=[...r.text.matchAll(/<code>([a-f0-9]{16})<\/code>/g)].map(x=>x[1]);ok(recovery.length===8,'Codes de secours');
r=await req('citoyen','page=logout',{csrf:csrf(r)});r=await login('citoyen','citoyen@test.test');ok(loc(r).includes('challenge'),'Connexion Yasmine soumise à 2FA');
for(const p of ['profile','security','change_password',...(group?['personal-history']:[])]){r=await req('citoyen','page='+p);ok(loc(r).includes('challenge'),'Pas de contournement par '+p);}
r=await req('citoyen','page=challenge');r=await req('citoyen','page=challenge',{csrf:csrf(r),code:recovery[0]});ok(loc(r).includes('home'),'Deuxième facteur valide');
r=await req('citoyen','page=change_password');r=await req('citoyen','page=change_password',{csrf_token:csrf(r),current_password:'TestMotDePasse42!',new_password:'NouveauMot42!',confirm_password:'NouveauMot42!'});ok(loc(r).includes('changed=1'),'Changement mot de passe Yasmine');
r=await req('citoyen','page=profile');ok(r.httpStatusCode===200,'Session courante conservée après changement');
let mails=php.readFileAsText('/app/yasmine/storage/mails.log');ok(mails.includes('Votre mot de passe a été modifié'),'Notification préparée (transport local de test)');
r=await req('reset','page=forgot_password');r=await req('reset','page=forgot_password',{csrf_token:csrf(r),email:'citoyen@test.test'});ok(loc(r).includes('sent=1'),'Demande de réinitialisation');
mails=php.readFileAsText('/app/yasmine/storage/mails.log');const reset=mails.match(/reset_password&token=([a-f0-9]{64})/)[1];
r=await req('reset','page=reset_password&token='+reset);r=await req('reset','page=reset_password&token='+reset,{csrf_token:csrf(r),token:reset,new_password:'ApresReset42!',confirm_password:'ApresReset42!'});ok(loc(r).includes('reset=1'),'Réinitialisation réussie');
r=await req('citoyen','page=profile');ok(loc(r).includes('login'),'Ancienne session invalidée après reset');
r=await req('reset','page=reset_password&token='+reset);ok(!r.text.includes('name="new_password"'),'Lien à usage unique');
r=await login('citoyen','citoyen@test.test','ApresReset42!');ok(loc(r).includes('challenge'),'Reset ne désactive pas la 2FA');
r=await req('citoyen','page=challenge');r=await req('citoyen','page=challenge',{csrf:csrf(r),code:recovery[0]});ok(r.httpStatusCode===422,'Code de secours non réutilisable');
r=await req('citoyen','page=challenge');r=await req('citoyen','page=challenge',{csrf:csrf(r),code:recovery[1]});ok(loc(r).includes('home'),'Autre code de secours valide');
r=await req('guest','page=login');r=await req('guest','page=oauth_start',{csrf:csrf(r),provider:'google'});ok(loc(r).startsWith('https://accounts.google.com/')&&loc(r).includes('code_challenge='),'Google conserve PKCE');
r=await req('guest','page=oauth_callback&provider=google&state=faux&code=faux');ok(r.httpStatusCode===422,'Retour OAuth falsifié refusé');
r=await req('guest','page=login');r=await req('guest','page=oauth_start',{csrf:csrf(r),provider:'facebook'});ok(loc(r).startsWith('https://www.facebook.com/'),'Facebook disponible');
// Canonical identities must refer to the same user as the password controller.
ok(await run("require '/app/amir/config/bootstrap.php';$m=new UtilisateurPersistant(AmirDatabase::connexion());$u=$m->creerSocial('Compte Social','social@test.test','google','subject-test');$p=(new AuthController())->findById($u['id']);echo ($p->getEmail()==='social@test.test' && $u['id_role']===1 && (int)$m->idSocial('google','subject-test')===$u['id'])?'ok':'non';")==='ok','Compte social visible par Yasmine avec rôle citoyen');
const table=group?'utilisateur':'users',status=group?'statut':'status',id=group?'id_user':'id';
await run(`require '/app/amir/config/database.php';AmirDatabase::connexion()->exec("UPDATE ${table} SET ${status}='bloque' WHERE ${id}=3");`);
r=await req('plombier','page=profile');ok(loc(r).includes('login'),'Blocage appliqué à la session existante');
if(group){
 for(const p of ['citoyens','plombiers','stats','global-history','personal-history']){r=await req('admin','page='+p);ok(r.httpStatusCode===200,'Module groupe '+p);}
 for(const role of ['C','P']){r=await req('admin','page=export-users&role='+role);ok(r.httpStatusCode===200&&r.headers['content-disposition'][0].includes('AquaFlow_liste_') && (!process.env.AQUAFLOW_TEST_XLSX || r.headers['content-disposition'][0].includes('.xlsx')),'Export '+role);}
 for(const p of ['global-history','stats','export-users&role=C']){r=await req('citoyen','page='+p);ok(r.httpStatusCode===403,'Accès groupe refusé '+p);}
 r=await req('citoyen','page=personal-history');ok(r.httpStatusCode===200&&!r.text.includes('admin@test.test'),'Historique personnel isolé');
}
if(group){
 r=await req('signup','page=signin');r=await req('signup','page=signup',{csrf_token:csrf(r),first_name:'Nouveau',last_name:'Plombier',email:'inscription@test.test',phone:'23112233',password:'CompteTest42!',role:'plumber',service_area:'Tunis',experience:'3'});
 ok(loc(r).includes('signin'),'Inscription plombier conservée');
 const uid=await run("require '/app/amir/models/UtilisateurPersistant.php';echo (new UtilisateurPersistant(AmirDatabase::connexion()))->getByEmail('inscription@test.test')['id'];");
 r=await req('signup','page=login');r=await req('signup','page=login',{csrf_token:csrf(r),email:'inscription@test.test',password:'CompteTest42!'});ok(r.httpStatusCode===200&&!loc(r),'Compte en attente ne se connecte pas');
 r=await req('admin','page=admin');r=await req('admin','page=admin',{csrf_token:csrf(r),id_user:uid,action:'approve'});ok(loc(r).includes('admin'),'Approbation administrateur conservée');
 r=await login('signup','inscription@test.test','CompteTest42!');ok(loc(r).includes('home'),'Compte approuvé se connecte');
 r=await req('signup','page=edit');r=await req('signup','page=edit',{csrf:csrf(r),nom:'Plombier',prenom:'Nouveau',email:'inscription@test.test',telephone:'23112233',adresse:'Tunis'});ok(loc(r).includes('profile'),'Édition du profil raccordée');
 r=await req('signup','page=profile');ok(r.text.includes('Nouveau')&&r.text.includes('23112233'),'Profil mis à jour dans le template du groupe');
 r=await req('admin','page=global-history&q=Approbation&role=A');ok(r.httpStatusCode===200&&r.text.includes('Approbation compte'),'Recherche et filtre de rôle historique');
 for(const mode of ['before','during','after']){r=await req('admin','page=global-history&date=2026-11-24&date_mode='+mode);ok(r.httpStatusCode===200,'Filtre historique '+mode);}
 r=await req('signup','page=profile');r=await req('signup','page=profile',{csrf_token:csrf(r),deactivate:'1'});ok(loc(r).includes('logout'),'Désactivation du profil de Mariem');
 r=await req('signup','page=personal-history');ok(loc(r).includes('login'),'Désactivation révoque l’accès aux autres modules');
 const before=Number(await run("require '/app/amir/config/database.php';echo AmirDatabase::connexion()->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();"));
 r=await req('signup','page=signin');r=await req('signup','page=signup',{csrf_token:csrf(r),first_name:'Faux',last_name:'Admin',email:'faux@test.test',phone:'24112233',password:'CompteTest42!',role:'admin'});
 const after=Number(await run("require '/app/amir/config/database.php';echo AmirDatabase::connexion()->query('SELECT COUNT(*) FROM utilisateur')->fetchColumn();"));ok(before===after,'Inscription ne peut créer un administrateur');
 r=await req('admin','page=security');r=await req('admin','page=plombiers',{csrf_token:csrf(r),id_user:'1'});r=await req('admin','page=admin');ok(r.httpStatusCode===200,'Désactivation ne cible pas un administrateur');
}
const keys=generateKeyPairSync('rsa',{modulusLength:2048,publicKeyEncoding:{type:'spki',format:'pem'},privateKeyEncoding:{type:'pkcs8',format:'pem'}});
php.writeFile('/app/tests/private.pem',keys.privateKey);php.writeFile('/app/tests/public.pem',keys.publicKey);
for(const file of ['oauth','import']) {
 const unit=await php.run({scriptPath:'/app/tests/'+file+'.php',env:{AQUAFLOW_TEST_DB:'/app/amir/var/'+file+'.sqlite'}});
 if(unit.errors||unit.exitCode)throw Error(unit.errors+unit.text);
 ok(Number(unit.text)>0,file+' : '+unit.text+' contrôles métier');
 console.log(file+' : '+unit.text+' contrôles métier réussis');
}
fs.writeFileSync(root+'/tests/resultats-'+(group?'groupe':'yasmine')+'.json',JSON.stringify({runtime:'PHP 8.4 WebAssembly, SQLite temporaire',services:'OAuth simulé, SMTP remplacé par journal privé de test',passed:results.length,checks:results},null,2)+'\n');
console.log(results.length+' contrôles réussis ('+(group?'groupe':'yasmine')+')');
