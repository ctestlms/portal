import java.lang.String.*;
import java.io.*;
import javax.swing.*;
import java.util.*;
import java.lang.*;
//////////////////////power class/////////////////////////////////////
class power
{
int powerval=-1;
public power(int pow)
{
powerval= pow;
}
}

////////////////////////////////////////class DataComm4//////////////////
class DataComm4
{
char alphabets[];
String  codes[];
int alpha;
ArrayList<power> powerlist =  new ArrayList<power>();
DataComm4()
{
 alpha=0;
}
public void makePowerList(String compress)
{
int powertemp=-1;
try{
 for(int i= compress.length()-1 ;i>=0;i--)
 {
  powertemp++;
   if(compress.charAt(i) == '1')
   {
   power node= new power(powertemp);
   powerlist.add(node);
   }
 }
 }
 catch(NullPointerException e)
{


}
}
public void displayPowerList()
{
  for(int i=0;i<powerlist.size();i++)
  {
   power node = powerlist.get(i);
   System.out.println(node.powerval+",");
  }
}
public void updatePowerList(int powerval)
{
  boolean found=false;
  for(int i=0;i<powerlist.size();i++)
  {
   power node = powerlist.get(i);
    if(node.powerval==powerval)
	{
	 found=true;
	 powerlist.remove(i);
	}
  }
   if(found == false)
    {
	 power node = new power(powerval);
	 powerlist.add(node);
	}
}
public int  getMaxPowerList()
{
  int max = -1;
  for(int i=0;i<powerlist.size();i++)
  {
   power node = powerlist.get(i);
    if(node.powerval>max)
	 max = node.powerval;
  }
return max;  
}
public String CRCENC(String enc)
{
powerlist.clear();
makePowerList(enc+"00000000");

int diff = getMaxPowerList() - 8;
 while(diff>=0)
  {
   updatePowerList(diff+8);
   updatePowerList(diff+2);
   updatePowerList(diff+1);
   updatePowerList(diff);
   diff = getMaxPowerList() - 8;
  }
return enc+makeRemainder();
 }
 
 
 public String makeRemainder()
 {
 StringBuffer remainder = new StringBuffer("00000000");
  for(int i=0;i<powerlist.size();i++)
  {
   power node = powerlist.get(i);
   remainder.setCharAt(remainder.length()-1-node.powerval,'1');
  }
  return remainder.toString();
 }
 
 
 
 public String CRCDEC(String enc)
{
powerlist.clear();
makePowerList(enc);

int diff = getMaxPowerList() - 8;
try{
 while(diff>=0)
  {
   updatePowerList(diff+8);
   updatePowerList(diff+2);
   updatePowerList(diff+1);
   updatePowerList(diff);
   diff = getMaxPowerList() - 8;
  }
 displayPowerList(); 
 if(powerlist.size()==0)
 {
   return enc.substring(0,enc.length()-8);
 }
 else
 System.out.println("ERROR IN DATA BITS ");
 
}
catch(NullPointerException e)
{
 System.exit(0);
}
return null;
}




public String encryption(String res)
{
 System.out.println("String length  :"+res.length());
 String encr="";
 for(int i=0;i<res.length();i++)
 {
  int val=search(res.charAt(i));
  if(val!=-1)
	{
	 encr=encr+codes[val];
	}

  }
  return encr;
}

public void setData(char alpha1[],int no)
{
 alpha = no;
 codes = new String[alpha];
 alphabets=new char[alpha];
  for(int i=0;i<alpha;i++)
  {
   alphabets[i]=alpha1[i];
  }
}

public String decryption(String enc)
{
 String curr="";
 String decr="";
 for(int i=0;i<enc.length();i++)
	{
	 curr=curr+enc.charAt(i);
	 int value=searchcode(curr);
		if(value!=-1)
		{
		 decr=decr+alphabets[value];
		 curr="";
		}
	}
 return decr;
}





public int search(char sy)
{
 for(int i=0;i<alpha;i++)
 {
  if(alphabets[i]==sy)
   return i;
 }
 return -1;
}

public int searchcode(String sy)
{
 for(int i=0;i<alpha;i++)
 {
  if((codes[i].equals(sy))==true)
    return i;
  }
 return -1;
}
public void displayCodes()
{
 System.out.println("");
 for(int i=0;i<alpha;i++)
  {
   System.out.println("SYMBOL : "+alphabets[i]+"    CODE : "+codes[i]);
  }
 System.out.println("");
}

//*******************************************************

public static void main(String[] argS) throws Exception
{
 int count[]=new int[30];
  System.out.println("\n\n\t\t PLEASE INSERT  characters  and . , and spaces only !!\n\n\n");
 String str=JOptionPane.showInputDialog(" Enter the characters  :");
 System.out.println("\n\n\t\t Message to be Encoded  : "+str);
 System.out.println("\n\n");
 for(int i=0;i<str.length();i++)
 {
  if(('a'==str.charAt(i))||('A'==str.charAt(i)))
    { 
     count[0]++;
    }
   if(('b'==str.charAt(i))||('B'==str.charAt(i)))
     {
      count[1]++;
     }
	if(('c'==str.charAt(i))||('C'==str.charAt(i)))
{
count[2]++;

}
if(('d'==str.charAt(i))||('d'==str.charAt(i)))
{
count[3]++;

}
if(('e'==str.charAt(i))||('E'==str.charAt(i)))
{
count[4]++;

}
if(('f'==str.charAt(i))||('F'==str.charAt(i)))
{
count[5]++;
}
if(('g'==str.charAt(i))||('G'==str.charAt(i)))
{
count[6]++;

}
if(('h'==str.charAt(i))||('H'==str.charAt(i)))
{
count[7]++;
}
if(('i'==str.charAt(i))||('I'==str.charAt(i)))
{
count[8]++;
}
if(('j'==str.charAt(i))||('J'==str.charAt(i)))
{
count[9]++;
}
if(('k'==str.charAt(i))||('K'==str.charAt(i)))
{
count[10]++;
}
if(('l'==str.charAt(i))||('L'==str.charAt(i)))
{
count[11]++;
}
if(('m'==str.charAt(i))||('M'==str.charAt(i)))
{
count[12]++;
}
if(('n'==str.charAt(i))||('N'==str.charAt(i)))
{
count[13]++;
}
if(('o'==str.charAt(i))||('O'==str.charAt(i)))
{
count[14]++;
}
if(('p'==str.charAt(i))||('P'==str.charAt(i)))
{
count[15]++;
}
if(('q'==str.charAt(i))||('Q'==str.charAt(i)))
{
count[16]++;
}
if(('r'==str.charAt(i))||('R'==str.charAt(i)))
{
count[17]++;
}
if(('s'==str.charAt(i))||('S'==str.charAt(i)))
{
count[18]++;
}
if(('t'==str.charAt(i))||('T'==str.charAt(i)))
{
count[19]++;
}
if(('u'==str.charAt(i))||('U'==str.charAt(i)))
{
count[20]++;
}
if(('v'==str.charAt(i))||('V'==str.charAt(i)))
{
count[21]++;
}
if(('w'==str.charAt(i))||('W'==str.charAt(i)))
{
count[22]++;
}
if(('x'==str.charAt(i))||('X'==str.charAt(i)))
{
count[23]++;
}
if(('y'==str.charAt(i))||('Y'==str.charAt(i)))
{
count[24]++;
}
if(('z'==str.charAt(i))||('Z'==str.charAt(i)))
{
count[25]++;
}
if('.'==str.charAt(i))
{
count[26]++;
}
if(' '==str.charAt(i))
{
count[27]++;
}
if(','==str.charAt(i))
{
count[28]++;
}
}// end of char assign for loop
float a=0;
System.out.print("The Frequency of each symbal is :");
for(int i=0;i<29;i++)
{
System.out.print(count[i]+" ");
}

System.out.println("\nLenght of the massage :"+str.length());

int alpha=0;
for (int i=0;i<29;i++)
{
if (count[i]!=0)
{
alpha++;
}
}
System.out.println("Alphabets included are :"+alpha);
int alphaEntropy=alpha;
char alphabets[]=new char [alpha];
int freq[]=new int [alpha];

float proFreq[]=new float [alpha];
float prob[]=new float [alpha];
int filler=0;
for (int i=0; i<29;i++)
{
if (count[i]!=0)
{
freq[filler]=count[i];
proFreq[filler]=count[i];
if (i==0)
alphabets[filler]='a';
else if (i==1)
alphabets[filler]='b';
else if (i==2)
alphabets[filler]='c';
else if (i==3)
alphabets[filler]='d';
else if (i==4)
alphabets[filler]='e';
else if (i==5)
alphabets[filler]='f';
else if (i==6)
alphabets[filler]='g';
else if (i==7)
alphabets[filler]='h';
else if (i==8)
alphabets[filler]='i';
else if (i==9)
alphabets[filler]='j';
else if (i==10)
alphabets[filler]='k';
else if (i==11)
alphabets[filler]='l';
else if (i==12)
alphabets[filler]='m';
else if (i==13)
alphabets[filler]='n';
else if (i==14)
alphabets[filler]='o';
else if (i==15)
alphabets[filler]='p';
else if (i==16)
alphabets[filler]='q';
else if (i==17)
alphabets[filler]='r';
else if (i==18)
alphabets[filler]='s';
else if (i==19)
alphabets[filler]='t';
else if (i==20)
alphabets[filler]='u';
else if (i==21)
alphabets[filler]='v';
else if (i==22)
alphabets[filler]='w';
else if (i==23)
alphabets[filler]='x';
else if (i==24)
alphabets[filler]='y';
else if (i==25)
alphabets[filler]='z';
else if (i==26)
alphabets[filler]='.';
else if (i==27)
alphabets[filler]=' ';
else if (i==28)
alphabets[filler]=',';
else
System.out.println(" not fond error ");
filler++;
}
}
for (int i=0;i<alpha;i++)
{
	
	prob[i]=proFreq[i]/(float)str.length();
	System.out.println(alphabets[i]+" occures "+freq[i]+" times and prob is : "+prob[i]);
}
//******************************************** data ready to be sorted
int t;
char temp;
for (int u=alpha-1;u>0;u--)		//for decending order
{
for (int i=0;i<u;i++)
{
if (freq[i]<freq[i+1])
{
t=freq[i];
freq[i]=freq[i+1];
freq[i+1]=t;

temp=alphabets[i];
alphabets[i]=alphabets[i+1];
alphabets[i+1]=temp;
}
}
}
System.out.println("\n");

for (int i=0;i<alpha;i++)
System.out.println(alphabets[i]+" occures "+freq[i]+" times");
int mid=str.length();
DataComm4 ob=new DataComm4();
ob.setData(alphabets,alpha);
String str1="";
ob.recursion(alphabets,freq,mid,alpha,str1);


System.out.println();
System.out.println();
ob.displayCodes();
String encoded=ob.encryption(str);

System.out.println("\n YOUR'S ENTERED BIT STREAM IS ======>> : "+encoded);
String enc = ob.CRCENC(encoded);
System.out.print("\n ENCODED BIT  STREAM IS ======>>");
System.out.println(enc);
String option=JOptionPane.showInputDialog("press  any Number to show decrpted data  :");
 int opt=Integer.parseInt(option);
 if(opt==1)
 {
 String struser=JOptionPane.showInputDialog(" PLEASE Enter the Bit Stream  :");
 enc = ob.CRCDEC(struser);
 }
 String dec = ob.CRCDEC(enc);
System.out.print("\n DECODED BIT  STREAM IS ======>> : ");
System.out.println(dec);
String message=ob.decryption(dec);
System.out.println("\n\t\t YOUR Decrypted Message IS ======>> : "+message);

int upper=enc.length();
int lower=str.length()*8;
float ratio=0;
ratio=(upper*100)/lower;
System.out.println("\n Encryption Ratio  :"+ratio+"%");
 
 double sum=0.0;
 for (int i=0;i<alphaEntropy;i++)
 {
	sum+=(prob[i]*(Math.log10(prob[i])/Math.log10(2)));
 }
 System.out.println("\n Entropy is : "+(sum*(-1))+"  bits/symbol");
 
}
//////////////////////////////////////////////////////////////////////////////////
public void recursion(char array[],int f[],int mid,int size,String str2)
{
	if(size==1)
	{
		int sear = search(array[0]);
		if(sear!=-1)
		codes[sear]=str2;
	}
	int m=0;
	int i=0;
	int f_l[]=new int[mid];
	int f_r[]=new int[mid];
	int set_l;
	int set_r;
	int check=0;
	char alphabets_l[] = new char[size];
	char alphabets_r[] = new char[size];
	for(i=0;i<size;i++)
	{
		check=check+f[i];

		if((check==mid/2)||(check>mid/2))
		{
			set_l=i;
		for(int j=0;j<=i;j++)
		{
			f_l[j]=f[j];
			alphabets_l[j]=array[j];
		}
		for(int k=i+1;k<size;k++)
		{
			f_r[m]=f[k];
			alphabets_r[m]=array[k];
			m++;
		}	
		}
		if(m!=0)
		{
			for(int out=0;out<=i;out++)
			System.out.print(alphabets_l[out]+",");
			System.out.println(str2+"0");
			for(int out1=0;out1<m;out1++)
			System.out.print(alphabets_r[out1]+",");		
			System.out.println(str2+"1");
			System.out.println();
	
			recursion(alphabets_l,f_l,check,i+1,str2+"0");
			System.out.println("RIGHT");
			recursion(alphabets_r,f_r,mid-check,m,str2+"1");
			break;
		}
	}
}
///////////////////////////////////////////////////////////////////////////////////
}

