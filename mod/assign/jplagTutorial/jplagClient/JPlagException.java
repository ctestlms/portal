// This class was generated by the JAXRPC SI, do not edit.
// Contents subject to change without notice.
// JAX-RPC Standard Implementation (1.1.3, build R1)
// Generated source version: 1.1.3

package jplagTutorial.jplagClient;


public class JPlagException extends java.lang.Exception {
    private java.lang.String exceptionType;
    private java.lang.String description;
    private java.lang.String repair;
    
    
    public JPlagException(java.lang.String exceptionType, java.lang.String description, java.lang.String repair) {
        this.exceptionType = exceptionType;
        this.description = description;
        this.repair = repair;
    }
    
    public java.lang.String getExceptionType() {
        return exceptionType;
    }
    
    public java.lang.String getDescription() {
        return description;
    }
    
    public java.lang.String getRepair() {
        return repair;
    }
}
