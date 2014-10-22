// This class was generated by the JAXRPC SI, do not edit.
// Contents subject to change without notice.
// JAX-RPC Standard Implementation (1.1.3, build R1)
// Generated source version: 1.1.3

package jplagTutorial.jplagClient;


import com.sun.xml.rpc.encoding.*;
import com.sun.xml.rpc.encoding.soap.SOAPConstants;
import com.sun.xml.rpc.encoding.soap.SOAP12Constants;
import com.sun.xml.rpc.soap.message.SOAPFaultInfo;
import com.sun.xml.rpc.streaming.*;
import com.sun.xml.rpc.wsdl.document.schema.SchemaConstants;
import javax.xml.namespace.QName;

public class JPlagTyp_setMailTemplate_Fault_SOAPSerializer extends SOAPFaultInfoSerializer {
    private static final javax.xml.namespace.QName ns1_JPlagException_QNAME = new QName("http://jplag.ipd.kit.edu/JPlagService/types", "JPlagException");
    private static final javax.xml.namespace.QName ns1_JPlagException_TYPE_QNAME = new QName("http://jplag.ipd.kit.edu/JPlagService/types", "JPlagException");
    private CombinedSerializer ns1_myJPlagException_LiteralSerializer;
    private CombinedSerializer ns1_myJPlagException_LiteralSerializer_Serializer;
    private static final int JPLAGTUTORIAL_JPLAGCLIENT_JPLAGEXCEPTION_INDEX = 0;
    
    public JPlagTyp_setMailTemplate_Fault_SOAPSerializer(boolean encodeType, boolean isNullable) {
        super(encodeType, isNullable);
    }
    
    public void initialize(InternalTypeMappingRegistry registry) throws java.lang.Exception {
        super.initialize(registry);
        ns1_myJPlagException_LiteralSerializer = (CombinedSerializer)registry.getSerializer("", jplagTutorial.jplagClient.JPlagException.class, ns1_JPlagException_TYPE_QNAME);
        ns1_myJPlagException_LiteralSerializer_Serializer = ns1_myJPlagException_LiteralSerializer.getInnermostSerializer();
    }
    
    protected java.lang.Object deserializeDetail(SOAPDeserializationState state, XMLReader reader,
        SOAPDeserializationContext context, SOAPFaultInfo instance) throws java.lang.Exception {
        boolean isComplete = true;
        javax.xml.namespace.QName elementName;
        javax.xml.namespace.QName elementType = null;
        SOAPInstanceBuilder builder = null;
        java.lang.Object detail = null;
        java.lang.Object obj = null;
        
        reader.nextElementContent();
        if (reader.getState() == XMLReader.END)
            return deserializeDetail(reader, context);
        XMLReaderUtil.verifyReaderState(reader, XMLReader.START);
        elementName = reader.getName();
        elementType = getType(reader);
        if (elementName.equals(ns1_JPlagException_QNAME)) {
            if (elementType == null || 
                (elementType.equals(ns1_myJPlagException_LiteralSerializer.getXmlType()) ||
                (ns1_myJPlagException_LiteralSerializer instanceof ArraySerializerBase &&
                elementType.equals(SOAPConstants.QNAME_ENCODING_ARRAY)) ) ) {
                obj = ns1_myJPlagException_LiteralSerializer.deserialize(ns1_JPlagException_QNAME, reader, context);
                detail = obj;
                reader.nextElementContent();
                skipRemainingDetailEntries(reader);
                XMLReaderUtil.verifyReaderState(reader, XMLReader.END);
                return (isComplete ? (Object)detail : (Object)state);
            } 
        }
        return deserializeDetail(reader, context);
    }
    
    protected void serializeDetail(java.lang.Object detail, XMLWriter writer, SOAPSerializationContext context)
        throws java.lang.Exception {
        if (detail == null) {
            throw new SerializationException("soap.unexpectedNull");
        }
        writer.startElement(DETAIL_QNAME);
        
        boolean pushedEncodingStyle = false;
        if (encodingStyle != null) {
            context.pushEncodingStyle(encodingStyle, writer);
        }
        if (detail instanceof jplagTutorial.jplagClient.JPlagException) {
            ns1_myJPlagException_LiteralSerializer_Serializer.serialize(detail, ns1_JPlagException_QNAME, null, writer, context);
        }
        writer.endElement();
        if (pushedEncodingStyle) {
            context.popEncodingStyle();
        }
    }
}
