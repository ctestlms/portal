// This class was generated by the JAXRPC SI, do not edit.
// Contents subject to change without notice.
// JAX-RPC Standard Implementation (1.1.3, build R1)
// Generated source version: 1.1.3

package jplagTutorial.jplagClient;


public class UserInfo {
    protected int leftSubmissionSlots;
    protected java.util.Calendar expires;
    protected java.lang.String email;
    protected java.lang.String emailSecond;
    protected java.lang.String homepage;
    
    public UserInfo() {
    }
    
    public UserInfo(int leftSubmissionSlots, java.util.Calendar expires, java.lang.String email, java.lang.String emailSecond, java.lang.String homepage) {
        this.leftSubmissionSlots = leftSubmissionSlots;
        this.expires = expires;
        this.email = email;
        this.emailSecond = emailSecond;
        this.homepage = homepage;
    }
    
    public int getLeftSubmissionSlots() {
        return leftSubmissionSlots;
    }
    
    public void setLeftSubmissionSlots(int leftSubmissionSlots) {
        this.leftSubmissionSlots = leftSubmissionSlots;
    }
    
    public java.util.Calendar getExpires() {
        return expires;
    }
    
    public void setExpires(java.util.Calendar expires) {
        this.expires = expires;
    }
    
    public java.lang.String getEmail() {
        return email;
    }
    
    public void setEmail(java.lang.String email) {
        this.email = email;
    }
    
    public java.lang.String getEmailSecond() {
        return emailSecond;
    }
    
    public void setEmailSecond(java.lang.String emailSecond) {
        this.emailSecond = emailSecond;
    }
    
    public java.lang.String getHomepage() {
        return homepage;
    }
    
    public void setHomepage(java.lang.String homepage) {
        this.homepage = homepage;
    }
}