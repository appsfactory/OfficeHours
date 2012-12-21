/*
 * web.cpp
 *
 *  Created on: Nov 9, 2012
 *      Author: adamwootton
 */
#include "web.hpp"
#include <QObject>
#include <QIODevice>
#include <QDebug>
#include <bb/cascades/Application>
#include <bb/cascades/QmlDocument>
#include <bb/cascades/AbstractPane>

using namespace bb::cascades;

web::web()
{
	mNetworkAccessManager = new QNetworkAccessManager(this);
	bool result = connect(mNetworkAccessManager,
			SIGNAL(finished(QNetworkReply*)),
			this, SLOT(requestFinished(QNetworkReply*)));
	Q_ASSERT(result);
	Q_UNUSED(result);

}
void web::initiateRequest(QString username) {
	QNetworkRequest request = QNetworkRequest();
	request.setUrl(QUrl("http://10.172.69.32/API.php"));
	qDebug() << "USERNAME" << username;
	mNetworkAccessManager->get(request);
}
void web::requestFinished(QNetworkReply* reply) {
	if (reply->error() == QNetworkReply::NoError){
		qDebug() <<  QString("\n Successful connection");
	} else {
		qDebug() << "\n Problem with the network";
	}
}
