import bb.cascades 1.0
Page{
    Container{
        id:pagecontainer
	    Container{
	        id:container
	        layout: StackLayout {
	            orientation: LayoutOrientation.TopToBottom
	        }
	        topPadding: 0
	        verticalAlignment: VerticalAlignment.Center
	        horizontalAlignment: HorizontalAlignment.Center
	        layoutProperties: StackLayoutProperties {
	        }
	        leftPadding: 100.0
	        rightPadding: 100.0
	        Rectangle {
			     width: 100
			     height: 100
			     color: "red"
			     border.color: "black"
			     border.width: 5
			     radius: 10
			 }
	        Label {
	            text: "Office"
	            textStyle.fontSizeValue: 47.0
	            textStyle.textAlign: TextAlign.Center
	            textStyle.color: Color.Black
	            multiline: false
	            topMargin: 0.0
	            textStyle.lineHeight: 0.9
	        }
	        Label {
	            text: "Hours"
	            textStyle.fontSizeValue: 30.0
	            textStyle.textAlign: TextAlign.Right
	            textStyle.color: Color.Black
	            multiline: false
	            horizontalAlignment: HorizontalAlignment.Fill
	            bottomMargin: 100.0
	            textStyle.lineHeight: 0.9
	        }
	        TextArea {
	            id:login
	            hintText: "username"
	            text: "test"
	        }
	        TextArea {
	            id:password
	            hintText: "password"
	            text: "test"
	            input.submitKey: SubmitKey.Go
	            inputMode: TextAreaInputMode.Text
	        }
	        Button {
	            verticalAlignment: VerticalAlignment.Center
	            horizontalAlignment: HorizontalAlignment.Center
	            text: "Log In"
	            onClicked: {
	                login.text = "worked"
	                pagecontainer.remove(container);
	                var mainPage = mainDef.createObject();
	                pagecontainer.add(mainPage);
	                web.initiateRequest(login.text);
	            }
	        }
	        
	    }
	    attachedObjects: [
            ComponentDefinition {
                id: mainDef
                source: "main.qml"
            }
        ]
	}
}
