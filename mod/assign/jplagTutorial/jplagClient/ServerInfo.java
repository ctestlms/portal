// This class was generated by the JAXRPC SI, do not edit.
// Contents subject to change without notice.
// JAX-RPC Standard Implementation (1.1.3, build R1)
// Generated source version: 1.1.3

package jplagTutorial.jplagClient;


public class ServerInfo {
    protected jplagTutorial.jplagClient.UserInfo userInfo;
    protected jplagTutorial.jplagClient.LanguageInfo[] languageInfos;
    protected java.lang.String[] countryLanguages;
    protected jplagTutorial.jplagClient.Submission[] submissions;
    
    public ServerInfo() {
    }
    
    public ServerInfo(jplagTutorial.jplagClient.UserInfo userInfo, jplagTutorial.jplagClient.LanguageInfo[] languageInfos, java.lang.String[] countryLanguages, jplagTutorial.jplagClient.Submission[] submissions) {
        this.userInfo = userInfo;
        this.languageInfos = languageInfos;
        this.countryLanguages = countryLanguages;
        this.submissions = submissions;
    }
    
    public jplagTutorial.jplagClient.UserInfo getUserInfo() {
        return userInfo;
    }
    
    public void setUserInfo(jplagTutorial.jplagClient.UserInfo userInfo) {
        this.userInfo = userInfo;
    }
    
    public jplagTutorial.jplagClient.LanguageInfo[] getLanguageInfos() {
        return languageInfos;
    }
    
    public void setLanguageInfos(jplagTutorial.jplagClient.LanguageInfo[] languageInfos) {
        this.languageInfos = languageInfos;
    }
    
    public java.lang.String[] getCountryLanguages() {
        return countryLanguages;
    }
    
    public void setCountryLanguages(java.lang.String[] countryLanguages) {
        this.countryLanguages = countryLanguages;
    }
    
    public jplagTutorial.jplagClient.Submission[] getSubmissions() {
        return submissions;
    }
    
    public void setSubmissions(jplagTutorial.jplagClient.Submission[] submissions) {
        this.submissions = submissions;
    }
}