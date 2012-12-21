// Default empty project template
#include "OfficeHours.hpp"
#include "web.hpp"
#include <bb/cascades/Application>
#include <bb/cascades/QmlDocument>
#include <bb/cascades/AbstractPane>

using namespace bb::cascades;

OfficeHours::OfficeHours(bb::cascades::Application *app)
: QObject(app)
{
    // create scene document from main.qml asset
    // set parent to created document to ensure it exists for the whole application lifetime
    qml = QmlDocument::create("asset:///login.qml").parent(this);
    QmlDocument *main = QmlDocument::create("asset:///main.qml").parent(this);
    web* myWeb = new web();
    qml->setContextProperty("web", myWeb);
    qml->setContextProperty("app", this);
    // create root object for the UI
    AbstractPane *root = qml->createRootObject<AbstractPane>();
    application = app;
    // set created root object as a scene
    //app->setScene(root);
    //changeScreen(qml);
    app->setScene(root);
    QObject *night = root->findChild<QObject*>("night");
    	if (night){
    		//night->setProperty("opacity", 0.0);
    	}
}

void OfficeHours::changeScreen(AbstractPane *scene){
	application->setScene(scene);
}
