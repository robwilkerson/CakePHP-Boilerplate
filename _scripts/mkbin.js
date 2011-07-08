//
// $Id$
//
// Creates the "bin" directory structure for a new project.  Execute
// this script for Windows deployments.
//
// Usage:  mkinst.js <instance root>
//

var args = WScript.arguments;

if (args.count() < 1) {
	WScript.echo ( "Usage: mkbin.js <instance root>" );
	WScript.quit();
}

var fso   = new ActiveXObject("Scripting.FileSystemObject");
var where = args(0);

if ( !fso.FolderExists ( where ) ) {
	WScript.echo ("The folder " + where + " does not exist.");
	WScript.quit();
}

var binDir = fso.BuildPath ( where, "bin" );

if ( !fso.FolderExists ( binDir ) )
	fso.CreateFolder ( binDir );

var tmpDir = fso.BuildPath ( binDir, "_tmp" );

if ( !fso.FolderExists ( tmpDir ) )
	fso.CreateFolder ( tmpDir );

// create the a-z structure for the /bin folder
for ( var L1 = "a".charCodeAt(0); L1 <= "z".charCodeAt(0); L1++ ) {
	var firstLevel = fso.BuildPath( binDir, String.fromCharCode(L1) );

	if ( !fso.FolderExists ( firstLevel ) )
		fso.CreateFolder ( firstLevel );

	for ( var L2 = "a".charCodeAt(0); L2 <= "z".charCodeAt(0); L2++ ) {
		var secondLevel = fso.BuildPath ( firstLevel, String.fromCharCode(L2) );

		if ( !fso.FolderExists ( secondLevel ) )
			fso.CreateFolder ( secondLevel );
	}
}
