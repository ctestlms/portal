// This class was generated by the JAXRPC SI, do not edit.
// Contents subject to change without notice.
// JAX-RPC Standard Implementation (1.1.3, build R1)
// Generated source version: 1.1.3

package jplagTutorial.jplagClient;

import com.sun.xml.rpc.encoding.*;
import com.sun.xml.rpc.encoding.xsd.XSDConstants;
import com.sun.xml.rpc.encoding.literal.*;
import com.sun.xml.rpc.encoding.literal.DetailFragmentDeserializer;
import com.sun.xml.rpc.encoding.simpletype.*;
import com.sun.xml.rpc.encoding.soap.SOAPConstants;
import com.sun.xml.rpc.encoding.soap.SOAP12Constants;
import com.sun.xml.rpc.streaming.*;
import com.sun.xml.rpc.wsdl.document.schema.SchemaConstants;
import javax.xml.namespace.QName;
import java.util.List;
import java.util.ArrayList;

public class SetUserDataParams_LiteralSerializer extends LiteralObjectSerializerBase implements Initializable  {
    private static final javax.xml.namespace.QName ns1_userdata_QNAME = new QName("", "userdata");
    private static final javax.xml.namespace.QName ns3_UserData_TYPE_QNAME = new QName("http://www.ipd.uni-karlsruhe.de/jplag/types", "UserData");
    private CombinedSerializer ns3_myUserData_LiteralSerializer;
    private static final javax.xml.namespace.QName ns1_oldUsername_QNAME = new QName("", "oldUsername");
    private static final javax.xml.namespace.QName ns2_string_TYPE_QNAME = SchemaConstants.QNAME_TYPE_STRING;
    private CombinedSerializer ns2_myns2_string__java_lang_String_String_Serializer;
    
    public SetUserDataParams_LiteralSerializer(javax.xml.namespace.QName type, java.lang.String encodingStyle) {
        this(type, encodingStyle, false);
    }
    
    public SetUserDataParams_LiteralSerializer(javax.xml.namespace.QName type, java.lang.String encodingStyle, boolean encodeType) {
        super(type, true, encodingStyle, encodeType);
    }
    
    public void initialize(InternalTypeMappingRegistry registry) throws Exception {
        ns3_myUserData_LiteralSerializer = (CombinedSerializer)registry.getSerializer("", jplagTutorial.jplagClient.UserData.class, ns3_UserData_TYPE_QNAME);
        ns2_myns2_string__java_lang_String_String_Serializer = (CombinedSerializer)registry.getSerializer("", java.lang.String.class, ns2_string_TYPE_QNAME);
    }
    
    public java.lang.Object doDeserialize(XMLReader reader,
        SOAPDeserializationContext context) throws java.lang.Exception {
        jplagTutorial.jplagClient.SetUserDataParams instance = new jplagTutorial.jplagClient.SetUserDataParams();
        java.lang.Object member=null;
        javax.xml.namespace.QName elementName;
        java.util.List values;
        java.lang.Object value;
        
        reader.nextElementContent();
        elementName = reader.getName();
        if (reader.getState() == XMLReader.START) {
            if (elementName.equals(ns1_userdata_QNAME)) {
                member = ns3_myUserData_LiteralSerializer.deserialize(ns1_userdata_QNAME, reader, context);
                if (member == null) {
                    throw new DeserializationException("literal.unexpectedNull");
                }
                instance.setUserdata((jplagTutorial.jplagClient.UserData)member);
                reader.nextElementContent();
            } else {
                throw new DeserializationException("literal.unexpectedElementName", new Object[] { ns1_userdata_QNAME, reader.getName() });
            }
        }
        else {
            throw new DeserializationException("literal.expectedElementName", reader.getName().toString());
        }
        elementName = reader.getName();
        if (reader.getState() == XMLReader.START) {
            if (elementName.equals(ns1_oldUsername_QNAME)) {
                member = ns2_myns2_string__java_lang_String_String_Serializer.deserialize(ns1_oldUsername_QNAME, reader, context);
                instance.setOldUsername((java.lang.String)member);
                reader.nextElementContent();
            } else {
                throw new DeserializationException("literal.unexpectedElementName", new Object[] { ns1_oldUsername_QNAME, reader.getName() });
            }
        }
        else {
            throw new DeserializationException("literal.expectedElementName", reader.getName().toString());
        }
        
        XMLReaderUtil.verifyReaderState(reader, XMLReader.END);
        return (java.lang.Object)instance;
    }
    
    public void doSerializeAttributes(java.lang.Object obj, XMLWriter writer, SOAPSerializationContext context) throws java.lang.Exception {
        jplagTutorial.jplagClient.SetUserDataParams instance = (jplagTutorial.jplagClient.SetUserDataParams)obj;
        
    }
    public void doSerialize(java.lang.Object obj, XMLWriter writer, SOAPSerializationContext context) throws java.lang.Exception {
        jplagTutorial.jplagClient.SetUserDataParams instance = (jplagTutorial.jplagClient.SetUserDataParams)obj;
        
        if (instance.getUserdata() == null) {
            throw new SerializationException("literal.unexpectedNull");
        }
        ns3_myUserData_LiteralSerializer.serialize(instance.getUserdata(), ns1_userdata_QNAME, null, writer, context);
        ns2_myns2_string__java_lang_String_String_Serializer.serialize(instance.getOldUsername(), ns1_oldUsername_QNAME, null, writer, context);
    }
}