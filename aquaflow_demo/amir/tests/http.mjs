import {php,root,base,assert,results} from './runtime.mjs';
import fs from 'node:fs';
const env={AQUAFLOW_TEST_DB:base+'/var/http.sqlite'};
const initial=await php.run({env,code:`<?php require '${base}/models/UtilisateurPersistant.php';$db=AmirDatabase::connexion();$db->exec(file_get_contents('${base}/tests/sqlite.sql'));$m=new UtilisateurPersistant($db);$m->create('Admin','admin@test.test','TestMotDePasse42!',3);$m->create('Citoyen','citoyen@test.test','TestMotDePasse42!',1);$m->create('Plombier','plombier@test.test','TestMotDePasse42!',2);`});
if(initial.exitCode||initial.errors)throw Error(initial.errors+initial.text);
const jars={};
async function request(client,page,post){
    const headers={host:'localhost',...(jars[client]?{cookie:jars[client]}:{})};
    if(post)headers['content-type']='application/x-www-form-urlencoded';
    const r=await php.run({scriptPath:base+'/public/index.php',relativeUri:'/amir/public/index.php?'+page,method:post?'POST':'GET',body:post?new TextEncoder().encode(new URLSearchParams(post).toString()):undefined,headers,env,$_SERVER:{REMOTE_ADDR:client}});
    for(const c of r.headers['set-cookie']||[]){if(c.startsWith('AQUAFLOW_AMIR='))jars[client]=c.split(';')[0];}
    if(r.errors)throw Error(r.errors);
    return r;
}
function token(r){const m=r.text.match(/name="csrf" value="([^"]+)"/);if(!m)throw Error('Jeton absent : '+r.text);return m[1];}
function location(r){return (r.headers.location||[])[0]||'';}
async function login(client,email){let r=await request(client,'page=login');const before=jars[client];r=await request(client,'page=login',{csrf:token(r),email,password:'TestMotDePasse42!'});assert(r.httpStatusCode===303&&location(r).includes('profile'),'Connexion '+client);assert(jars[client]!==before,'Renouvellement de session '+client);}
async function run(code){const r=await php.run({env,code:'<?php '+code});if(r.errors||r.exitCode)throw Error(r.errors+r.text);return r.text;}
let r=await request('guest','page=details&id=2');assert(r.httpStatusCode===303&&location(r).includes('login'),'Anonyme redirigé avant la fiche');
r=await request('guest','page=login',{});assert(r.httpStatusCode===403,'CSRF obligatoire pour se connecter');
r=await request('guest','page=login');assert(r.text.includes('Google')&&r.text.includes('Facebook'),'Boutons Google et Facebook présents');
assert((r.headers['cache-control']||[]).includes('no-store'),'Réponses sensibles non mises en cache');
assert((r.headers['referrer-policy']||[]).includes('no-referrer'),'Aucun lien OAuth transmis par Referer');
const fresh=await request('cookie-check','page=login');
assert((fresh.headers['set-cookie']||[]).some(x=>/HttpOnly/i.test(x)&&/SameSite=Lax/i.test(x)),'Cookie HttpOnly et SameSite');
r=await request('guest','page=oauth_start',{csrf:token(r),provider:'google'});assert(r.httpStatusCode===303&&location(r).startsWith('https://accounts.google.com/')&&location(r).includes('code_challenge='),'Redirection OAuth Google');
r=await request('guest','page=oauth_callback&provider=google&state=faux&code=faux');assert(r.httpStatusCode===422,'Callback OAuth forgé refusé');
r=await request('guest','page=oauth_start');assert(r.httpStatusCode===405,'OAuth ne démarre pas sans formulaire');
await login('admin','admin@test.test');await login('citizen','citoyen@test.test');await login('plumber','plombier@test.test');
for(const client of ['citizen','plumber'])for(const page of ['details&id=1','citizens','plumbers','stats']){r=await request(client,'page='+page);assert(r.httpStatusCode===403,client+' interdit : '+page);}
for(const page of ['citizens','plumbers','stats','details&id=2','details&id=3','profile','security']){r=await request('admin','page='+page);assert(r.httpStatusCode===200,'Admin ouvre '+page);}
r=await request('admin','page=details&id=2');assert(r.text.includes('Citoyen')&&!r.text.includes('$2y$')&&!r.text.includes('totp_secret'),'Fiche sans mot de passe ni secret 2FA');
r=await request('admin','page=citizens');assert(r.text.includes('page=details&amp;id=2'),'Liste et fiche reliées');
r=await request('admin','page=details&id=1%20OR%201=1');assert(r.httpStatusCode===400,'Injection dans ID refusée');
r=await request('admin','page=details&id=999');assert(r.httpStatusCode===404,'Fiche absente : 404');
r=await request('admin','page=details&id[]=2');assert(r.httpStatusCode===400,'ID tableau refusé');
r=await request('citizen','page=profile');assert(r.text.includes('Mes informations personnelles')&&r.text.includes('citoyen@test.test'),'Vue profil de Mariem réutilisée');
r=await request('citizen','page=edit');r=await request('citizen','page=edit',{csrf:token(r),nom:'<script>alert(1)</script>',email:'citoyen@test.test',telephone:'22123456',adresse:'Tunis'});assert(r.httpStatusCode===302&&location(r).includes('profile'),'Modification via le contrôleur de Mariem');
r=await request('citizen','page=profile');assert(r.httpStatusCode===200&&r.text.includes('&lt;script&gt;')&&!r.text.includes('<script>alert(1)</script>'),'Profil persistant et XSS échappée');
r=await request('citizen','page=security');r=await request('citizen','page=security',{csrf:token(r),action:'prepare'});assert(r.httpStatusCode===303,'Préparation 2FA');
r=await request('citizen','page=security');const secret=r.text.match(/class="secret">([^<]+)</)[1];assert(/^[A-Z2-7]{32}$/.test(secret),'Clé Google Authenticator de vingt octets');
r=await request('citizen','page=security',{csrf:token(r),action:'enable',code:'bad'});assert(r.httpStatusCode===422,'Activation sans bon code refusée');
r=await request('citizen','page=security');const code=await run(`require '${base}/controllers/TotpController.php';echo AmirTotp::code('${secret}',intdiv(time(),30));`);
r=await request('citizen','page=security',{csrf:token(r),action:'enable',code});assert(r.httpStatusCode===303,'Activation 2FA');
r=await request('citizen','page=security');const codes=[...r.text.matchAll(/<code>([a-f0-9]{16})<\/code>/g)].map(x=>x[1]);assert(codes.length===8,'Huit codes de secours affichés');
r=await request('citizen','page=security');assert(!r.text.includes(codes[0]),'Codes de secours visibles une seule fois');
r=await request('citizen','page=logout',{csrf:token(r)});assert(r.httpStatusCode===303,'Déconnexion');
r=await request('citizen','page=login');r=await request('citizen','page=login',{csrf:token(r),email:'citoyen@test.test',password:'TestMotDePasse42!'});assert(location(r).includes('challenge'),'Mot de passe mène au deuxième facteur');
r=await request('citizen','page=profile');assert(location(r).includes('challenge'),'Profil interdit pendant le deuxième facteur');
r=await request('citizen','page=challenge');r=await request('citizen','page=challenge',{csrf:token(r),code:'abcdefabcdefabcd'});assert(r.httpStatusCode===422,'Faux code de secours refusé');
r=await request('citizen','page=challenge');r=await request('citizen','page=challenge',{csrf:token(r),code:codes[0]});assert(location(r).includes('profile'),'Connexion terminée avec secours');
r=await request('citizen','page=security');r=await request('citizen','page=security',{csrf:token(r),action:'disable',code:codes[1]});assert(r.httpStatusCode===303,'Désactivation contrôlée de la 2FA');
r=await request('citizen','page=security');assert(r.text.includes('Configurer Google Authenticator'),'2FA désactivée');
r=await request('citizen','page=deactivate');r=await request('citizen','page=deactivate',{csrf:token(r)});assert(location(r).includes('deactivated'),'Désactivation via le contrôleur de Mariem');
r=await request('citizen','page=profile');assert(location(r).includes('login'),'Compte désactivé déconnecté');
r=await request('guest','page=login');r=await request('guest','page=login',{csrf:token(r),email:'citoyen@test.test',password:'TestMotDePasse42!'});assert(r.httpStatusCode===422,'Compte inactif ne se reconnecte pas');
r=await request('admin','page=logout');assert(r.httpStatusCode===405,'Déconnexion en GET interdite');
r=await request('admin','page=inconnue');assert(r.httpStatusCode===404,'Route inconnue : 404');
const units=JSON.parse(php.readFileAsText(base+'/tests/unit-results.json'));
fs.writeFileSync(root+'/aquaflow_demo/amir/docs/resultats-tests.json',JSON.stringify({runtime:'PHP 8.4.27 WebAssembly et SQLite temporaire',externalServices:'Simulés ; aucun compte Google/Facebook réel utilisé',tests:units.length+results.length,unit:units,http:results},null,2));
console.log(results.length+' vérifications HTTP réussies ; total : '+(units.length+results.length));
