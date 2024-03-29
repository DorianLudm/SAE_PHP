// var noise = new SimplexNoise();
// var visualizerRunning = false;

// var audio;
// var analyser;

// function loadFichier(fichier) {
//     if (!visualizerRunning) { // Vérifier si aucun visualiseur n'est en cours d'exécution
//         audio = fichier;
//         analyser = Howler.ctx.createAnalyser();
//     }
// }

// function reset () {
//     document.getElementById('out').innerHTML = '';
// }

// function playVisualize() {
//     if(visualizerRunning){
//         document.getElementById('out').innerHTML = '';
//         cancelAnimationFrame(render);
//     }
//     visualizerRunning = true;
//     Howler.masterGain.connect(analyser);
//     analyser.fftSize = 512;
//     var bufferLength = analyser.frequencyBinCount;
//     var dataArray = new Uint8Array(bufferLength);
//     var scene = new THREE.Scene();
//     var group = new THREE.Group();
//     var camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
//     camera.position.set(0,0,100);
//     camera.lookAt(scene.position);
//     scene.add(camera);

//     var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
//     renderer.setSize(window.innerWidth, window.innerHeight);

//     var planeGeometry = new THREE.PlaneGeometry(800, 800, 20, 20);
//     var planeMaterial = new THREE.MeshLambertMaterial({
//         color: 0x07484f,
//         side: THREE.DoubleSide,
//         wireframe: true
//     });

//     var plane = new THREE.Mesh(planeGeometry, planeMaterial);
//     plane.rotation.x = -0.5 * Math.PI;
//     plane.position.set(0, 30, 0);
//     group.add(plane);

//     var plane2 = new THREE.Mesh(planeGeometry, planeMaterial);
//     plane2.rotation.x = -0.5 * Math.PI;
//     plane2.position.set(0, -30, 0);
//     group.add(plane2);

//     var icosahedronGeometry = new THREE.IcosahedronGeometry(10, 4);
//     var lambertMaterial = new THREE.MeshLambertMaterial({
//         color: 0x0e6c76,
//         wireframe: true
//     });

//     var ball = new THREE.Mesh(icosahedronGeometry, lambertMaterial);
//     ball.position.set(0, 0, 0);
//     group.add(ball);

//     var ambientLight = new THREE.AmbientLight(0xaaaaaa);
//     scene.add(ambientLight);

//     var spotLight = new THREE.SpotLight(0xffffff);
//     spotLight.intensity = 0.9;
//     spotLight.position.set(-10, 40, 20);
//     spotLight.lookAt(ball);
//     spotLight.castShadow = true;
//     scene.add(spotLight);

//     scene.add(group);

//     try{
//         let aside = document.querySelector('aside');
//         let header = document.getElementById('trueHeader');
//         aside.style.display = 'none';
//         header.style.display = 'none';
//         document.body.requestFullscreen();
//         document.body.style.overflow = 'hidden';
//         var elem = document.getElementById('out');
//         elem.requestFullscreen();
//         elem.appendChild(renderer.domElement);
//         modeVisualizer();
//     } catch(e) {
//         console.log(e);
//     }

//     window.addEventListener('resize', onWindowResize, false);
//     render();

//     function render() {
//         analyser.getByteFrequencyData(dataArray);

//         var lowerHalfArray = dataArray.slice(0, (dataArray.length/2) - 1);
//         var upperHalfArray = dataArray.slice((dataArray.length/2) - 1, dataArray.length - 1);

//         var overallAvg = avg(dataArray);
//         var lowerMax = max(lowerHalfArray);
//         var lowerAvg = avg(lowerHalfArray);
//         var upperMax = max(upperHalfArray);
//         var upperAvg = avg(upperHalfArray);

//         var lowerMaxFr = lowerMax / lowerHalfArray.length;
//         var lowerAvgFr = lowerAvg / lowerHalfArray.length;
//         var upperMaxFr = upperMax / upperHalfArray.length;
//         var upperAvgFr = upperAvg / upperHalfArray.length;

//         makeRoughGround(plane, modulate(upperAvgFr, 0, 1, 0.5, 4));
//         makeRoughGround(plane2, modulate(lowerMaxFr, 0, 1, 0.5, 4));

//         makeRoughBall(ball, modulate(Math.pow(lowerMaxFr, 0.8), 0, 1, 0, 8), modulate(upperAvgFr, 0, 1, 0, 4));

//         group.rotation.y += 0.005;
//         renderer.render(scene, camera);
//         requestAnimationFrame(render);
//     }

//     function onWindowResize() {
//         camera.aspect = window.innerWidth / window.innerHeight;
//         camera.updateProjectionMatrix();
//         renderer.setSize(window.innerWidth, window.innerHeight);
//     }

//     function makeRoughBall(mesh, bassFr, treFr) {
//         mesh.geometry.vertices.forEach(function (vertex, i) {
//             var offset = mesh.geometry.parameters.radius;
//             var amp = 7;
//             var time = window.performance.now();
//             vertex.normalize();
//             var rf = 0.00001;
//             var distance = (offset + bassFr ) + noise.noise3D(vertex.x + time *rf*7, vertex.y +  time*rf*8, vertex.z + time*rf*9) * amp * treFr;
//             vertex.multiplyScalar(distance);
//         });
//         mesh.geometry.verticesNeedUpdate = true;
//         mesh.geometry.normalsNeedUpdate = true;
//         mesh.geometry.computeVertexNormals();
//         mesh.geometry.computeFaceNormals();
//     }

//     function makeRoughGround(mesh, distortionFr) {
//         mesh.geometry.vertices.forEach(function (vertex, i) {
//             var amp = 2;
//             var time = Date.now();
//             var distance = (noise.noise2D(vertex.x + time * 0.0003, vertex.y + time * 0.0001) + 0) * distortionFr * amp;
//             vertex.z = distance;
//         });
//         mesh.geometry.verticesNeedUpdate = true;
//         mesh.geometry.normalsNeedUpdate = true;
//         mesh.geometry.computeVertexNormals();
//         mesh.geometry.computeFaceNormals();
//     }
// }

//     document.body.addEventListener('touchend', function(ev) { Howler.ctx.resume(); });

//     function fractionate(val, minVal, maxVal) {
//         return (val - minVal)/(maxVal - minVal);
//     }

//     function modulate(val, minVal, maxVal, outMin, outMax) {
//         var fr = fractionate(val, minVal, maxVal);
//         var delta = outMax - outMin;
//         return outMin + (fr * delta);
//     }

//     function avg(arr){
//         var total = arr.reduce(function(sum, b) { return sum + b; });
//         return (total / arr.length);
//     }

//     function max(arr){
//         return arr.reduce(function(a, b){ return Math.max(a, b); })
//     }

// var player = document.getElementById('customPlayer');

// function modeVisualizer() {
//     player.style.transition = 'opacity 0.5s ease';

//     player.style.opacity = 0;

//     document.body.addEventListener('mouseenter', function() {
//         player.style.opacity = 1;
//     });

//     var timeout;

//     document.addEventListener('mousemove', function() {
//         clearTimeout(timeout);
//         player.style.opacity = 1;

//         timeout = setTimeout(function() {
//             player.style.opacity = 0;
//         }, 5000);
//     });

//     document.body.addEventListener('mouseleave', function() {
//         player.style.opacity = 0;
//     });
// }

// function deleteVisualizer() {
//     visualizerRunning = false;
//     document.getElementById('out').innerHTML = '';
//     document.body.style.overflow = 'auto';
//     player.style.opacity = 1;

//     document.body.addEventListener('mouseenter', function() {
//         player.style.opacity = 1;
//     });


//     document.addEventListener('mousemove', function() {
//         player.style.opacity = 1;
//     });

//     document.body.addEventListener('mouseleave', function() {
//         player.style.opacity = 1;
//     });
// }

// export { loadFichier };
// export { playVisualize };
// export { reset };
// export { deleteVisualizer };