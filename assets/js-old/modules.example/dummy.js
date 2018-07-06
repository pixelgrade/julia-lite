var myModule = function ( myPublicName, mySecretName ) {

    var secretName = mySecretName;

    var privateFunction = function() {
        console.log( "Name: " + secretName );
    };

    this.publicSetName = function( newName ) {
        secretName = newName;
	    privateFunction();
    };

	this.publicName = myPublicName;

	this.getName = function() {
		return secretName;
	};

};

myModule.prototype.myStaticMethod = function() {
	// ...
};

myModule.prototype.anotherStaticMethod = function() {
	// ...
};
