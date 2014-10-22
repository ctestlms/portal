// This class was generated by the JAXRPC SI, do not edit.
// Contents subject to change without notice.
// JAX-RPC Standard Implementation (1.1.3, build R1)
// Generated source version: 1.1.3

package jplagTutorial.jplagClient;

import com.sun.xml.rpc.encoding.*;
import com.sun.xml.rpc.client.ServiceExceptionImpl;
import com.sun.xml.rpc.util.exception.*;
import com.sun.xml.rpc.soap.SOAPVersion;
import com.sun.xml.rpc.client.HandlerChainImpl;
import javax.xml.rpc.*;
import javax.xml.rpc.encoding.*;
import javax.xml.rpc.handler.HandlerChain;
import javax.xml.rpc.handler.HandlerInfo;
import javax.xml.namespace.QName;

public class JPlagService_Impl extends com.sun.xml.rpc.client.BasicService implements JPlagService {
    private static final QName serviceName = new QName("http://jplag.ipd.kit.edu/JPlagService", "JPlagService");
    private static final QName ns1_JPlagServicePort_QNAME = new QName("http://jplag.ipd.kit.edu/JPlagService", "JPlagServicePort");
    private static final Class JPlagTyp_PortClass = jplagTutorial.jplagClient.JPlagTyp.class;
    
    public JPlagService_Impl() {
        super(serviceName, new QName[] {
                        ns1_JPlagServicePort_QNAME
                    },
            new jplagTutorial.jplagClient.JPlagService_SerializerRegistry().getRegistry());
        
        {
            java.util.List handlerInfos = new java.util.Vector();
            {
                java.util.Map props = new java.util.HashMap();
                javax.xml.namespace.QName[] headers = null;
                HandlerInfo handlerInfo = new HandlerInfo(jplagTutorial.util.JPlagClientAccessHandler.class, props, headers);
                handlerInfos.add(handlerInfo);
            }
            getHandlerRegistry().setHandlerChain(ns1_JPlagServicePort_QNAME, handlerInfos);
        }
    }
    
    public java.rmi.Remote getPort(javax.xml.namespace.QName portName, java.lang.Class serviceDefInterface) throws javax.xml.rpc.ServiceException {
        try {
            if (portName.equals(ns1_JPlagServicePort_QNAME) &&
                serviceDefInterface.equals(JPlagTyp_PortClass)) {
                return getJPlagServicePort();
            }
        } catch (Exception e) {
            throw new ServiceExceptionImpl(new LocalizableExceptionAdapter(e));
        }
        return super.getPort(portName, serviceDefInterface);
    }
    
    public java.rmi.Remote getPort(java.lang.Class serviceDefInterface) throws javax.xml.rpc.ServiceException {
        try {
            if (serviceDefInterface.equals(JPlagTyp_PortClass)) {
                return getJPlagServicePort();
            }
        } catch (Exception e) {
            throw new ServiceExceptionImpl(new LocalizableExceptionAdapter(e));
        }
        return super.getPort(serviceDefInterface);
    }
    
    public jplagTutorial.jplagClient.JPlagTyp getJPlagServicePort() {
        java.lang.String[] roles = new java.lang.String[] {"http://schemas.xmlsoap.org/soap/actor/next"};
        HandlerChainImpl handlerChain = new HandlerChainImpl(getHandlerRegistry().getHandlerChain(ns1_JPlagServicePort_QNAME));
        handlerChain.setRoles(roles);
        jplagTutorial.jplagClient.JPlagTyp_Stub stub = new jplagTutorial.jplagClient.JPlagTyp_Stub(handlerChain);
        try {
            stub._initialize(super.internalTypeRegistry);
        } catch (JAXRPCException e) {
            throw e;
        } catch (Exception e) {
            throw new JAXRPCException(e.getMessage(), e);
        }
        return stub;
    }
}
