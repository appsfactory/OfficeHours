// Default empty project template
#ifndef OfficeHours_HPP_
#define OfficeHours_HPP_

#include <QObject>
#include <bb/cascades/AbstractPane>
#include <bb/cascades/QmlDocument>
namespace bb { namespace cascades { class Application; }}

/*!
 * @brief Application pane object
 *
 *Use this object to create and init app UI, to create context objects, to register the new meta types etc.
 */
class OfficeHours : public QObject
{
    Q_OBJECT
public:
    OfficeHours(bb::cascades::Application *app);
    Q_INVOKABLE void changeScreen(bb::cascades::AbstractPane *scene);
    virtual ~OfficeHours() {}
private:
 bb::cascades::Application *application;
 bb::cascades::QmlDocument *qml;
};

#endif /* OfficeHours_HPP_ */
