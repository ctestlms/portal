/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author sam
 */

//import static java.lang.System;
import java.util.Scanner;
import java.util.Arrays;

public class ShananFano {
    static String temp;
    static String data;
    static String freq;
    static int [] freq1;

    public static void main(String []args){
       input();
       MainComp();
       entropy(freq1);
    }



    public static int func(int l,int []arr){
        int left=0,right=0;

        for(int i=0;i<=l;i++)
            left+=arr[i];
        int t1=left+arr[l+1];

        for(int j=l+1;j<arr.length;j++)
            right+=arr[j];
        int t2=right-arr[l+1];

        if(Math.abs(left-right)<Math.abs(t1-t2))
            return 1;
        else
            return 0;
    }



    public static void input(){
        int i=0;
        int word_count=0;
        temp="";
        System.out.println("Enter the paraagraph");
        Scanner inp = new Scanner(System.in);
        String para=inp.nextLine();
        para=para.toUpperCase();
        while(i<para.length() && word_count<100){
            if(i>0)
            if(para.charAt(i)==' ' && ( para.charAt(i-1)>='A' && para.charAt(i-1)<='Z' ))
                word_count++;
            else if( (para.charAt(i)<'A' || para.charAt(i)>'Z') && (para.charAt(i)!='.' && para.charAt(i)!=',' && para.charAt(i)!=' ') ){
                System.out.println("Worng input enter again");
                temp+=para.substring(0,i);
                System.out.println(temp);
                //inp.reset();
                para=inp.nextLine();
                para=para.toUpperCase();
                i=0;
            }
            i++;
        }
    }



    public static void MainComp(){          /// MAIN PART A starts from here ////
         
        char []arr=temp.toCharArray();
        Arrays.sort(arr);                           // Sortig the array in ascending order //
        temp=new String(arr);                       // Making a new String equals to temp //
        char ch;
        int i=0;

        data="";


        // Compressing Algo //
        while(i<temp.length()){
            ch=temp.charAt(i);
            int last=temp.lastIndexOf(ch);
            data+=ch;
            i=last+1;
        }

        freq1=new int[data.length()];
        int j=0;
        while(j<freq1.length){
            ch=data.charAt(j);
            int first=temp.indexOf(ch);
            int last=temp.lastIndexOf(ch);
            freq1[j]=last-first+1;
            j++;
        }

        Arrays.sort(freq1);
        j=freq1.length;
        for(i=0;i<=freq1.length/2;i++,j--)
            freq1[i]=freq1[j];

        ///// fucntion starts for making groups ////
        for(int l=0;l<freq1.length;l++){
            int chk=func(l,freq1);
            if (chk==0)
                continue;
            if(l>1)

        }

    }




    public static void entropy(int arr[])           ////// ENTROPY FUNCTION /////
    {
        double a=0.0;
        for(int i=0;i<arr.length;i++)
        a= -(a+arr[i]*(Math.log(arr[i])/Math.log(2)));
                System.out.println("Entropy of text is :"+a);
    }



}
