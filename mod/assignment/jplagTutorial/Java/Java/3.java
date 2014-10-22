
			/*----------------------------------------------*/
			/*				Main  Class						*/
			/*----------------------------------------------*/


import java.util.Scanner;
import java.util.Arrays;
import java.lang.Math;					//This package is included To find Logrithem

public class qasim_usman_zaid
{

	public static void main(String[] args)
	{

	Scanner input=new Scanner(System.in);
	String input_stream;
			
	System.out.println("Please enter a paragraph of text: ");	//Ask user for the input 
	input_stream=input.nextLine();		

	
	String charlist=sfclass.compress(input_stream);			//the charlist used in the string
									//are returned
	
	int[]frequency=sfclass.frequency_gen(input_stream,charlist);	//Frequency of the char are been
	System.out.println("\nInput Test:\n"+input_stream);		//found and returned
	

	int total_char=charlist.length();				//Total Char used
	char[] charuse=new char[total_char];
	

	char chr[]=charlist.toCharArray();				//storing charecter used in
									//an array so that we can compare
		for(int i=0;i< total_char;i++)				//it individually
		{		
		 charuse[i]=chr[i];
		}




//------------------------------------------------------------------------------------------------------
// Probability of each charecter is found by calling calling the function and returned
//------------------------------------------------------------------------------------------------------
		

	float[] probability=new float[total_char];
	float input_length=input_stream.length();
	probability=sfclass.prob(frequency,total_char,input_length);
	
//------------------------------------------------------------------------------------------------------
// Array of frequencies are sorted by bubble sort, 
// accourdingly char array and probability array are arranged
//------------------------------------------------------------------------------------------------------

   for(int a = 1; a <  total_char; ++a)
    {
       for(int b = ( total_char)-1; b >= a; --b)
      {
         if(frequency[ b - 1] < frequency[ b ]) 
	{
       
          int temp = frequency[ b - 1];
	  frequency[ b - 1] = frequency[ b ];
	  frequency[ b ] = temp;

	  char tempc=charuse[b-1];        
	  charuse[ b - 1] = charuse[ b ];        
	  charuse[ b ] = tempc;

	  
          float tempp = probability[ b - 1];
	  probability[ b - 1] = probability[ b ];
	  probability[ b ] = tempp;


        }
      }
    }

	
	
//------------------------------------------------------------------------------------------------------
// Bit code of each character is found and been returned, for fuction code see Class file
//------------------------------------------------------------------------------------------------------
		
	String [] codeword=new String[frequency.length];	

	for(int s=0;s<frequency.length;s++)
	{
		codeword[s]="";		
	}

	if(frequency.length==1)				//if there is only one type of char use
	codeword[0]="0";				//i simply assign '0' to it
	
	else		
	codeword=sfclass.codeword_gen(codeword,frequency, 0,frequency.length);


//------------------------------------------------------------------------------------------------------
// Character used there frequencies, probability and there Bit code are displyed in a table
//------------------------------------------------------------------------------------------------------

	System.out.println("\nS.no    Character    Frequency   Probability   Bit Code\n");


	for(int i=0;i< total_char;i++)
	{
	System.out.println(i+"\t   "+charuse[i]+"\t\t"+frequency[i]+"\t  "+probability[i]+"\t   "+codeword[i]);
	}

	


//------------------------------------------------------------------------------------------------------
// Entropy is returned by calling the function, function code is in class  file
//------------------------------------------------------------------------------------------------------

	float entr=sfclass.entropy(probability);
	
	System.out.println("\nEntropy : "+entr+"\n");


//------------------------------------------------------------------------------------------------------
// Compressio Ratio is returned by calling the function, function code is in class  file
//------------------------------------------------------------------------------------------------------


	int ratio=sfclass.compratio(input_stream.length(),codeword,frequency.length,frequency);

	System.out.println("Compression Ratio : "+ratio+"%");


//------------------------------------------------------------------------------------------------------
// Average symbol/bit is returned by calling the function, function code is in class  file
//------------------------------------------------------------------------------------------------------

	float avg=sfclass.average(codeword,probability);
	
	System.out.println("\nAverage Bit/Symbol : "+avg);

//------------------------------------------------------------------------------------------------------
// Resulting output bitstream is returned by calling the function,for  function code see class  file
//------------------------------------------------------------------------------------------------------

	String output_stream="";

	output_stream=sfclass.stream_output(codeword,input_stream,charuse);
		
	System.out.println("\nOutput Bit Stream: \n"+output_stream);

//------------------------------------------------------------------------------------------------------
// A given bitstream is decompressed and returned by calling the function,Accourdinly to the above assign
// bit code.for function code see class  file
//------------------------------------------------------------------------------------------------------

	String decomp_string="";

	decomp_string=sfclass.decompress(output_stream,codeword,charuse);

	System.out.println("\nDecompress String: \n"+decomp_string);


//------------------------------------------------------------------------------------------------------
//                               ###################### MAIN END ########################
//------------------------------------------------------------------------------------------------------

}

//------------------------------------------------------------------------------------------------------
//                            ####################  Main Class End ########################
//------------------------------------------------------------------------------------------------------
}


			/*##############################################################################*/
			/*																				*/	
			/*							Shannon Fano Class									*/
			/*				All the function Used above are written in this class			*/
			/*																				*/
			/*##############################################################################*/
class sfclass
{

	
//------------------------------------------------------------------------------------
//####### This Method Find And Seperate The Characters From The Input String##########
//------------------------------------------------------------------------------------
  
  static public String compress(String input)
  {
		
	String temp=input;
	String charlist="";	
	

	for(int i=0;(i+1)<temp.length();i++)
	{	
	 String remaining_string=temp;			//This string stores the remaining char
	 temp="";					//left after removing the similar char 
	 						//from the string
	 char aChar1 = remaining_string.charAt(i);	
	 
	    if(remaining_string.length()==1)		//if only one char,left no need of
	    {						// comparison
	     charlist=charlist+aChar1;
	     break;
	    }
	
	 char aChar2 = remaining_string.charAt(i+1);	
	 		
	    for(int j=1;j<remaining_string.length();j++)	//This loop compare the chars
	    {		  		
	      if(aChar1==aChar2)
	      {			    
		if((j+1)<remaining_string.length())		
		aChar2 = remaining_string.charAt(j+1);
			
		else					
		break;
			
	      }
				
	      else 
	      {					
	       temp=temp+aChar2;
			
	        if((j+1)<remaining_string.length())		
	         aChar2 = remaining_string.charAt(j+1);
			
	        else					
	         break;
	       }
    	    }  	
	
	
	 charlist=charlist+aChar1;		//stores a char use in the string after
	 i=-1;					//removing that char from the original string 
	}
	
 	if(temp.length()==1)
	charlist=charlist+temp;		
		
	
  return charlist;					//returns the charuse in the list

}


//------------------------------------------------------------------------------------
//####### This Method Find The Frequency of each Character The Input String###########
//------------------------------------------------------------------------------------

public static int[] frequency_gen(String input,String charlist)
{
    int [] freqlist=new int[charlist.length()];
    char chr[]=charlist.toCharArray();	    		//breaks the string into single char
													//array so that each char can be
													//compare in the string for frequency 
	for(int i=0;i<charlist.length();i++)
	{
	 int count=0;
		for(int j=0;j<input.length();j++)
		{
		
		 if(chr[i]==input.charAt(j))		//this condition count the total number
		 count++;							//of time a char been used
		
		}
	
	 freqlist[i]=count;				
	
	}		


 return freqlist;				       //return the frequency of all charracters

}






//------------------------------------------------------------------------------------
//############# This Method Generates Bit code for all Character Used ################
//------------------------------------------------------------------------------------

public static String [] codeword_gen(String [] codeword,int[] freq, int llimit,int hlimit)
{

  int sum=0;
	
  for(int i=llimit; i<hlimit;i++)
  sum=sum+freq[i];			    	//all the frequencies are added
	
  int ans=0;
  int index=(llimit-1);
	
	
  while(ans<(sum/2))			   	//this loop divied the frequencies 
  {					   				//array into to equal parts
   index++;
   ans=ans+freq[index];	
  }

  for(int x=llimit;x<=index;x++)		//Assign '0' to first half of the array
   codeword[x]=codeword[x]+'0';
	
  for(int y=(index+1);y<hlimit;y++)	   	//Assign '1'to the second half of the array
   codeword[y]=codeword[y]+'1';	

  if(hlimit==llimit)			    	//if there is only one type of char in String 
   codeword[llimit]=codeword[llimit]+'0';


  else if(hlimit-llimit==1)		    	//if there is only two char in the String
  {codeword[llimit]=codeword[llimit]+'0';	//Than assign '0' to first nd '1' to second
   codeword[hlimit]=codeword[hlimit]+'1';
  }

  else 					    				//recursively the functionis called, and two 
  {					    					//parts of he array are pass seperatly	
   if(index>llimit)
     codeword_gen(codeword,freq,llimit,(index+1));	

   if(hlimit-(index+1)>1)
     codeword_gen(codeword,freq,(index+1),hlimit);
	
  }
 
 return codeword;				  		  //return the string array containg the
						   				 //bit code for each character
}

//------------------------------------------------------------------------------------
//################## This Method Finds the Entropy and returns it#####################
//------------------------------------------------------------------------------------

public static float entropy(float[] prob)
{

  float entp=0;

	for(int i=0;i<prob.length;i++)
	{

	entp=entp+(float)(prob[i]*(Math.log(prob[i])/Math.log(2)));

	}

  return -entp;						//returns the entropy

}


//------------------------------------------------------------------------------------
//################## This Method Finds the Compression Ratio #########################
//------------------------------------------------------------------------------------

public static int compratio(int input,String[] code,int size,int[] frequency)
{
  float ans=0;
  float output=0;
  input=input*8;				    //each char input takes 8 bites 


	for(int i=0;i<size;i++)
	{
	 output=output+(code[i].length()*frequency[i]);  //finds the output bitsteam	
	}						 						//length
  System.out.println("Total Input Bits: "+input);
  System.out.println("Total output Bits: "+output);

  ans=(output/input)*100;						//Formula for comp ratio

  return (int)ans;								//return the answer

}


//------------------------------------------------------------------------------------
//################ This Method Finds the Average Bit\Symbol Used #####################
//------------------------------------------------------------------------------------

public static float average(String[] code,float[] prob)
{

float ans=0;

	for(int i=0;i<prob.length;i++)
	{
	
	ans=ans+(prob[i]*code[i].length());		//Formula to find Average 
											//bit\symbol
	}

return ans;


}

//------------------------------------------------------------------------------------
//############ This Method Finds the Probability of each char used ###################
//------------------------------------------------------------------------------------

public static float[] prob(int[]frequency,int total_char,float length)
{

 float[] probability=new float[total_char];

  for(int i=0;i< total_char;i++)
  {
   probability[i]=(frequency[i]/length);	//Formula to find Probability
  }

 return probability;

}




//------------------------------------------------------------------------------------
//############## This Method Finds the output Bit Stream and retuns it ###############
//------------------------------------------------------------------------------------

public static String stream_output(String[] codeword,String input_char,char[] charuse)
{
  String output_stream="";				//this stores the resulting bit stream

	for(int i=0;i<input_char.length();i++)
	{
	  for(int j=0;j<charuse.length;j++)
	  {
		if(input_char.charAt(i)==charuse[j])	  
		{
		  output_stream=output_stream+codeword[j];
		  break;
		
		}		

	
	  }

	}

return output_stream;

}


//------------------------------------------------------------------------------------
//#####This Method Decompress the String using the encoding scheme last used #########
//------------------------------------------------------------------------------------

public static String decompress(String output_stream,String[] codeword,char[] charuse)
{

String decomp_string="";
String comp="";

	for(int i=0;i<output_stream.length();i++)	
	{
	  comp=comp+output_stream.charAt(i);
	  for(int j=0;j<codeword.length;j++)
	  {
	   
	    if(comp.equals(codeword[j]))
	    {
		decomp_string=decomp_string+charuse[j];
		comp="";
		break;		
	    }
	
	  }

	}

return decomp_string;

}


//------------------------------------------------------------------------------------
//##################          End Of Shannon fano Class     #########################
//------------------------------------------------------------------------------------

}






